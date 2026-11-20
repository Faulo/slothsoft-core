<?php /** @noinspection PhpUndefinedClassInspection */
declare(strict_types = 1);

namespace Slothsoft\Core;

use COM;
use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMXPath;
use Exception;
use FInfo;
use Generator;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Util\DirectoryCouldNotBeCreatedException;
use Slothsoft\Core\Calendar\DateTimeFormatter;
use Slothsoft\Core\Configuration\StorageConfigurationField;
use SplFileInfo;

/**
 * Legacy file-system helper for path handling, directory scans, media metadata, and archive operations.
 *
 * @author Daniel Schulz
 * @since 2012-10-19
 */
abstract class FileSystem {
    
    const ZIP_PATH = 'C:/Program Files/7-Zip/7z.exe';
    
    const ZIP_QUERY_EXTRACT = 'e "%1$s" -y -o"%2$s"';
    
    const ZIP_QUERY_CREATE = 'a -t7z "%1$s" "%2$s" -mx0 -ms=off -mmt -y';
    
    const ZIP_QUERY_UPDATE = 'u -t7z "%1$s" "%2$s" -mx0 -ms=off -mmt -y';
    
    const FFMPEG_PATH = 'C:/NetzwerkDaten/Dropbox/Tools/ffmpeg/ffmpeg.exe';
    
    const SCANDIR_EXCLUDE_DIRS = 1;
    
    const SCANDIR_EXCLUDE_FILES = 2;
    
    const SCANDIR_EXCLUDE_HIDDEN = 16;
    
    const SCANDIR_REALPATH = 4;
    
    const SCANDIR_WEBPATH = 8;
    
    const SCANDIR_SORT = 32;
    
    const SCANDIR_FILEINFO = 64;
    
    const ARCHIVE_MAX_FILES = 1000;
    
    /** @var array<mixed> */
    public static array $sizeUnits = [
        'B',
        'kB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB'
    ];
    
    /** @var array<mixed> */
    public static array $videoExtensions = [
        'avi',
        'mkv',
        'mp4',
        'mov',
        'ogv',
        'wmv',
        'mpg',
        'webm',
        'm4v',
        'flv',
        'ogm'
    ];
    
    /** @var array<mixed> */
    public static array $audioExtensions = [
        'mp3',
        'wav',
        'ogg',
        'wma',
        'ogm',
        'mid',
        'm4a'
    ];
    
    /** @var array<mixed> */
    public static array $subttitleExtensions = [
        'vtt',
        'srt',
        'ssa',
        'ass'
    ];
    
    public static function ensureDirectory(string $directory): void {
        if (is_file($directory)) {
            unlink($directory);
        }
        
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
    
    public static function isVideo(string $fileName): bool {
        return in_array(strtolower(self::extension($fileName)), self::$videoExtensions);
    }
    
    public static function isAudio(string $fileName): bool {
        return in_array(strtolower(self::extension($fileName)), self::$audioExtensions);
    }
    
    public static function isSubttitle(string $fileName): bool {
        return in_array(strtolower(self::extension($fileName)), self::$subttitleExtensions);
    }
    
    /** @param mixed $size */
    public static function drawBytes($size, int $precision = 2): string {
        for ($i = 0; $size > 1024 && isset(self::$sizeUnits[$i + 1]); $i++) {
            $size /= 1024.0;
        }
        return sprintf('%.' . $precision . 'f %s', $size, self::$sizeUnits[$i]);
    }
    
    private static function storage(): StorageConfigurationField {
        static $field;
        if ($field === null) {
            $field = new StorageConfigurationField(new CacheDirectoryStorage());
        }
        return $field;
    }
    
    /** @return void */
    public static function setStorage(EphemeralStorageInterface $storage) {
        self::storage()->setValue($storage);
    }
    
    public static function getStorage(): EphemeralStorageInterface {
        return self::storage()->getValue();
    }
    
    /** @throws Exception */
    public static function generateStorageKey(string $path, string $hash = ''): string {
        $key = realpath($path);
        if (! $key) {
            throw new Exception(sprintf('PATH NOT FOUND: "%s"', $path));
        }
        if (strlen($hash)) {
            $key .= '#' . $hash;
        }
        return sprintf('file:///%s', $key);
    }
    
    /**
     * @throws DOMException
     * @throws Exception
     */
    public static function asNode(string $path, ?DOMDocument $dataDoc = null): ?DOMNode {
        $retNode = null;
        $isDir = is_dir($path);
        $returnDocument = ! $dataDoc;
        if ($path = realpath($path)) {
            $name = basename($path);
            if ($name === 'Thumbs.db') {
                @unlink($path);
            } else {
                if ($isDir) {
                    $modifyTime = self::changetime($path) ?? 0;
                    $storage = self::getStorage();
                    $storageKey = self::generateStorageKey($path, 'asNode-v2');
                    if ($tmpNode = $storage->retrieveXML($storageKey, $modifyTime, $dataDoc)) {
                        if (! $dataDoc) {
                            $dataDoc = $tmpNode->ownerDocument;
                        }
                        if ($tmpNode->firstChild instanceof DOMElement) {
                            $retNode = $tmpNode->removeChild($tmpNode->firstChild) ?: null;
                        }
                    }
                }
                if (! $dataDoc) {
                    $dataDoc = new DOMDocument();
                }
                if (! $retNode) {
                    $tagName = null;
                    $childNodes = [];
                    $attr = [];
                    if (is_file($path)) {
                        $tagName = 'file';
                        $attr['mime'] = self::mime($path);
                        $attr['size-int'] = self::size($path);
                        $attr['size-string'] = self::drawBytes($attr['size-int'] ?? 0);
                        $attr['ext'] = self::extension($path);
                        if (self::isVideo($path)) {
                            $attr['isVideo'] = '';
                            /*
                             * $mediaInfo = self::mediaInfo($path);
                             * $mediaTypes = ['video', 'audio', 'subtitle'];
                             * $parentNode = $dataDoc->createElement('mediaInfo');
                             * $childNodes[] = $parentNode;
                             * foreach ($mediaTypes as $type) {
                             * foreach ($mediaInfo[$type] as $key => $stream) {
                             * $node = $dataDoc->createElement('stream');
                             * $node->setAttribute('type', $type);
                             * $node->setAttribute('key', $key);
                             * foreach ($stream as $k => $v) {
                             * $node->setAttribute($k, $v);
                             * }
                             * $parentNode->appendChild($node);
                             * }
                             * }
                             * //
                             */
                        }
                        if (self::isAudio($path)) {
                            $attr['isAudio'] = '';
                        }
                        if (self::isSubttitle($path)) {
                            $attr['isSubttitle'] = '';
                        }
                    }
                    if (is_dir($path)) {
                        $tagName = 'folder';
                        $attr['size-int'] = 0;
                        // $attr['free-int'] = self::free($path);
                        // $attr['free-string'] = self::drawBytes($attr['free-int']);
                        $childList = self::scanDir($path, self::SCANDIR_REALPATH | self::SCANDIR_SORT);
                        foreach ($childList as $child) {
                            $node = self::asNode((string) $child, $dataDoc);
                            if ($node instanceof DOMElement) {
                                $attr['size-int'] += (int) $node->getAttribute('size-int');
                                $childNodes[] = $node;
                            }
                        }
                        $attr['size-string'] = self::drawBytes($attr['size-int']);
                    }
                    if ($tagName) {
                        $retNode = $dataDoc->createElement($tagName);
                        $time = self::changetime($path) ?? time();
                        $attr['change-datetime'] = date(DateTimeFormatter::FORMAT_DATETIME, $time);
                        $attr['change-utc'] = date(DateTimeFormatter::FORMAT_UTC, $time);
                        $time = self::maketime($path) ?? time();
                        $attr['make-datetime'] = date(DateTimeFormatter::FORMAT_DATETIME, $time);
                        $attr['make-utc'] = date(DateTimeFormatter::FORMAT_UTC, $time);
                        $attr['path'] = $path;
                        $attr['id'] = 'id-' . md5($path);
                        $attr['name'] = $name;
                        $attr['title'] = isset($attr['ext']) ? substr($attr['name'], 0, -(1 + strlen($attr['ext']))) : $attr['name'];
                        foreach ($attr as $key => $val) {
                            $val = (string) $val;
                            if (! mb_check_encoding($val, 'UTF-8')) {
                                $val = mb_convert_encoding($val, 'UTF-8', 'UTF-8') ?: $val;
                            }
                            $retNode->setAttribute($key, $val);
                        }
                        $retNode->setIdAttribute('id', true);
                        foreach ($childNodes as $node) {
                            $retNode->appendChild($node);
                        }
                        if ($isDir) {
                            $storage->storeXML($storageKey, $retNode, $modifyTime);
                        }
                    }
                }
                if ($returnDocument and $retNode) {
                    $dataDoc->appendChild($retNode);
                    $retNode = $dataDoc;
                }
            }
        }
        return $retNode;
    }
    
    public static function webpath(string $fileName, bool $absolute = true): string {
        return $absolute ? str_replace(DIRECTORY_SEPARATOR, '/', substr($fileName, strlen($_SERVER['DOCUMENT_ROOT']))) : str_replace(DIRECTORY_SEPARATOR, '/', substr($fileName, strlen(dirname($_SERVER['SCRIPT_FILENAME']) . '/')));
    }
    
    public static function size(string $fileName): ?int {
        if (is_readable($fileName)) {
            return self::parseByteCount(@filesize($fileName));
        }
        if ($file = self::lookupFile($fileName)) {
            return self::parseByteCount($file->Size);
        }
        return null;
    }
    
    public static function free(string $fileName): ?float {
        $space = disk_free_space($fileName);
        return is_float($space) ? $space : null;
    }
    
    public static function mime(string $fileName): ?string {
        $fInfo = new FInfo(FILEINFO_MIME_TYPE);
        @$ret = $fInfo->file($fileName);
        return is_string($ret) ? $ret : null;
    }
    
    public static function extension(string $fileName): string {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }
    
    public static function changetime(string $fileName): ?int {
        // $time = filemtime($fileName);
        $time = null;
        if (is_readable($fileName)) {
            $time = filemtime($fileName);
        } elseif ($file = self::lookupFile($fileName)) {
            $time = strtotime($file->DateLastModified);
        }
        return $time === false ? null : $time;
    }
    
    public static function maketime(string $fileName): ?int {
        // $time = filemtime($fileName);
        $time = null;
        if (is_readable($fileName)) {
            $time = filectime($fileName);
        } elseif ($file = self::lookupFile($fileName)) {
            $time = strtotime($file->DateLastModified);
        }
        return $time === false ? null : $time;
    }
    
    /** @return mixed A COM filesystem automation object, or null when unavailable. */
    public static function lookupFile(string $fileName) {
        if (! class_exists(COM::class)) {
            return null;
        }
        if (is_readable($fileName)) {
            $com = new COM('Scripting.FileSystemObject');
            return is_file($fileName) ? $com->GetFile($fileName) : $com->GetFolder($fileName);
        }
        $parent = dirname($fileName);
        if ($parent !== $fileName && ($folder = self::lookupFile($parent))) {
            $base = basename($fileName);
            foreach ($folder->files as $file) {
                if ($file->name === $base) {
                    return $file;
                }
            }
            foreach ($folder->SubFolders as $file) {
                if ($file->name === $base) {
                    return $file;
                }
            }
        }
        return null;
    }
    
    public static function filenameEncode(string $filename, bool $removeRoot = false): string {
        if ($removeRoot) {
            if (strpos($filename, ServerEnvironment::getRootDirectory()) === 0) {
                $filename = substr($filename, strlen(ServerEnvironment::getRootDirectory()));
            }
        }
        return str_replace([
            ':',
            '/',
            '\\',
            ' '
        ], '-', $filename);
    }
    
    public static function filenameSanitize(string $filename): string {
        $notAllowed = [
            ':',
            '\\',
            '/',
            '⁄',
            '*',
            '"',
            '?',
            '!',
            '<',
            '>',
            '|'
        ];
        $toReplace = [
            ' -',
            ''
        ];
        $filename = str_replace($notAllowed, $toReplace, $filename);
        $filename = htmlentities($filename, ENT_QUOTES, 'UTF-8');
        $filename = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~iu', '$1', $filename) ?: $filename;
        $filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');
        $filename = mb_convert_encoding($filename, 'UTF-8', 'UTF-8') ?: $filename;
        $filename = str_replace($notAllowed, $toReplace, $filename);
        $filename = preg_replace('~\s+~iu', ' ', $filename) ?: $filename;
        $filename = trim($filename);
        return $filename;
    }
    
    /**
     * @param mixed $filePath
     * @param mixed $downloadName
     * @return void
     */
    public static function download($filePath, $downloadName) {
        $handle = Utils::tryFopen($filePath, 'rb');
        try {
            $stat = fstat($handle);
            $size = $stat['size'] ?? null;
            if (! is_int($size) || $size < 0) {
                throw new RuntimeException('Could not determine a representable file size.');
            }
            
            $range = self::resolveDownloadRange($_SERVER['HTTP_RANGE'] ?? '', $size);
            header('Accept-Ranges: bytes');
            if ($range === []) {
                http_response_code(416);
                header("Content-Range: bytes */$size");
                header('Content-Length: 0');
                return;
            }
            
            [$start, $length] = $range ?? [0, $size];
            self::seekFile($handle, $start);
            http_response_code($range === null ? 200 : 206);
            if ($range !== null) {
                $end = $start + ($length - 1);
                header("Content-Range: bytes $start-$end/$size");
            }
            header('Connection: close');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($downloadName));
            header("Content-Length: $length");
            
            set_time_limit(0);
            foreach (self::readChunks($handle, $length) as $chunk) {
                echo $chunk;
                flush();
            }
        } finally {
            fclose($handle);
        }
    }
    
    /**
     * @return array{int, int}|array{}|null Offset and length, an empty array for an unsatisfiable range, or null to ignore the header.
     */
    private static function resolveDownloadRange(string $header, int $size): ?array {
        if (! preg_match('/\Abytes=([0-9]*)-([0-9]*)\z/i', trim($header), $matches) || ($matches[1] === '' && $matches[2] === '')) {
            return null;
        }
        if ($size === 0) {
            return [];
        }
        if ($matches[1] === '') {
            $length = min(self::parseUnsignedDecimal($matches[2]) ?? $size, $size);
            return $length === 0 ? [] : [$size - $length, $length];
        }
        $start = self::parseUnsignedDecimal($matches[1]);
        if ($start === null || $start >= $size) {
            return [];
        }
        $lastByte = $size - 1;
        $end = $matches[2] === '' ? $lastByte : min(self::parseUnsignedDecimal($matches[2]) ?? $lastByte, $lastByte);
        return $end < $start ? [] : [$start, $end - $start + 1];
    }
    
    private static function parseUnsignedDecimal(string $digits): ?int {
        $digits = ltrim($digits, '0');
        $max = (string) PHP_INT_MAX;
        if (strlen($digits) > strlen($max) || (strlen($digits) === strlen($max) && strcmp($digits, $max) > 0)) {
            return null;
        }
        return (int) $digits;
    }
    
    /** @param mixed $value */
    private static function parseByteCount($value): ?int {
        if (is_int($value)) {
            return $value >= 0 ? $value : null;
        }
        if (is_float($value)) {
            $limit = (float) PHP_INT_MAX + 1.0;
            return $value >= 0 && $value < $limit && floor($value) === $value ? (int) $value : null;
        }
        if (is_string($value)) {
            return self::parseUnsignedDecimal($value);
        }
        return null;
    }
    
    /** @param resource $handle */
    private static function seekFile($handle, int $offset): void {
        if (@fseek($handle, $offset) !== 0) {
            throw new RuntimeException("Could not seek to file offset $offset.");
        }
    }
    
    /**
     * @param resource $handle
     * @return Generator<int, string>
     */
    private static function readChunks($handle, int $length): Generator {
        while ($length > 0) {
            $chunk = fread($handle, min(1048576, $length));
            if ($chunk === false) {
                throw new RuntimeException('Could not read file chunk.');
            }
            if ($chunk === '') {
                break;
            }
            $length -= strlen($chunk);
            yield $chunk;
        }
    }
    
    /**
     * @param mixed $filePath
     * @param mixed $start
     * @param mixed $length
     * @return void
     */
    public static function outputChunk($filePath, $start, $length) {
        $offset = self::parseByteCount($start);
        $byteCount = self::parseByteCount($length);
        if ($offset === null || $byteCount === null) {
            throw new InvalidArgumentException('File offset and length must be nonnegative whole numbers within the native integer range.');
        }
        
        set_time_limit(0);
        $handle = Utils::tryFopen($filePath, 'rb');
        try {
            self::seekFile($handle, $offset);
            foreach (self::readChunks($handle, $byteCount) as $chunk) {
                echo dechex(strlen($chunk)) . "\n" . $chunk . "\n";
                flush();
            }
            echo "0\n            \n";
        } finally {
            fclose($handle);
        }
    }
    
    /**
     * List the contents of a directory.
     *
     * @return list<int|string|SplFileInfo>
     */
    public static function scanDir(string $relPath, int $options = 0, ?string $filter = null): array {
        $dirPath = realpath($relPath);
        if ($dirPath === false) {
            return [];
        }
        $dirPath .= DIRECTORY_SEPARATOR;
        $ret = array_diff(scandir($dirPath), [
            '.',
            '..'
        ]);
        $exclude = [];
        if ($options & self::SCANDIR_EXCLUDE_DIRS) {
            foreach ($ret as $file) {
                if (is_dir($dirPath . $file)) {
                    $exclude[] = $file;
                }
            }
        }
        if ($options & self::SCANDIR_EXCLUDE_FILES) {
            foreach ($ret as $file) {
                if (is_file($dirPath . $file)) {
                    $exclude[] = $file;
                }
            }
        }
        if ($options & self::SCANDIR_EXCLUDE_HIDDEN) {
            foreach ($ret as $file) {
                if ($file[0] === '.') {
                    $exclude[] = $file;
                }
                /*
                 * //TAKES FOREVER DO NOT USE
                 * $attributes = shell_exec('attrib ' . escapeshellarg($dirPath . $file));
                 * $attributes = substr($attributes, 0, 12);
                 * if (strpos($attributes, 'H') !== false) {
                 * $exclude[] = $file;
                 * }
                 * //
                 */
            }
        }
        if ($filter !== null) {
            foreach ($ret as $file) {
                if (! preg_match($filter, $file)) {
                    $exclude[] = $file;
                }
            }
        }
        $ret = array_values(array_diff($ret, $exclude));
        if ($options & self::SCANDIR_SORT) {
            $tmp = [];
            foreach ($ret as $val) {
                $tmp[$val] = preg_replace('/\.[^.]+$/', '', $val);
            }
            asort($tmp);
            $ret = array_keys($tmp);
        }
        if ($options & self::SCANDIR_REALPATH) {
            foreach ($ret as &$val) {
                $val = $dirPath . $val;
            }
            unset($val);
        } elseif ($options & self::SCANDIR_WEBPATH) {
            foreach ($ret as &$val) {
                $val = self::webpath($dirPath . $val);
            }
            unset($val);
        } elseif ($options & self::SCANDIR_FILEINFO) {
            foreach ($ret as &$val) {
                $val = new SplFileInfo($dirPath . $val);
            }
            unset($val);
        }
        return $ret;
    }
    
    /** @return non-empty-array<int|string, int|null>|false */
    public static function dirModifyTime(string $dirPath, ?string $rootPath = null) {
        if ($rootPath === null) {
            $dirPath = realpath($dirPath);
            if ($dirPath === false) {
                return false;
            }
            $rootPath = dirname($dirPath);
        }
        if (is_dir($dirPath)) {
            $rootLength = strlen($rootPath) + 1;
            $ret = [
                substr($dirPath, $rootLength) => self::changetime($dirPath)
            ];
            $arr = self::scanDir($dirPath);
            foreach ($arr as $file) {
                $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    $ret += self::dirModifyTime($filePath);
                } else {
                    $ret[substr($filePath, $rootLength)] = self::changetime($filePath);
                }
            }
            return $ret;
        }
        return false;
    }
    
    /**
     * @return array<mixed>
     * @throws Exception
     */
    public static function mediaInfo(string $filePath): array {
        $filePath = realpath($filePath);
        $ret = [
            'file' => $filePath,
            'video' => [],
            'audio' => [],
            'subtitle' => [],
            'attachment' => [],
            'data' => [],
            'unknown' => []
        ];
        if ($filePath) {
            $modifyTime = self::changetime($filePath) ?? 0;
            $storage = self::getStorage();
            $storageKey = self::generateStorageKey($filePath, 'mediaInfo');
            if ($mediaInfo = $storage->retrieveJSON($storageKey, $modifyTime)) {
                $ret = $mediaInfo;
            } else {
                $command = sprintf('%s -i "%s" 2>&1', self::FFMPEG_PATH, $filePath);
                $res = [];
                exec($command, $res);
                foreach ($res as $line) {
                    $match = [];
                    if (preg_match('/^\s*Stream #(\d+):(\d+)\(*(.*?)\)*:\s+(\w+):\s+([\w\d_]+)(.*)$/', $line, $match)) {
                        // array_shift($match);
                        // $ret[] = $match;
                        $key = $match[1] . ':' . $match[2];
                        $lang = $match[3];
                        $type = strtolower($match[4]);
                        $codec = $match[5];
                        $settings = $match[5] . $match[6];
                        if (! isset($ret[$type]) || ! is_array($ret[$type])) {
                            die($match[0]);
                        }
                        $ret[$type][$key] = [
                            'lang' => $lang,
                            'codec' => $codec,
                            'settings' => $settings
                        ];
                        $m = [];
                        if (preg_match('/(\d{2,4})x(\d{2,4})/', $settings, $m)) {
                            $ret[$type][$key]['width'] = $m[1];
                            $ret[$type][$key]['height'] = $m[2];
                        }
                    } elseif (preg_match('/^\s*Stream.+$/', $line, $match)) {
                        die($match[0]);
                    }
                }
                $storage->storeJSON($storageKey, $ret, $modifyTime);
            }
        }
        return $ret;
    }
    
    /**
     * @param mixed $archivePath
     * @param mixed $targetDirectory
     * @return bool
     * @throws Exception
     */
    public static function extractArchive($archivePath, $targetDirectory) {
        if (! is_readable(self::ZIP_PATH)) {
            throw new Exception('7-Zip not found @ ' . self::ZIP_PATH);
        }
        if (! is_file($archivePath)) {
            return false;
        }
        $command = '"' . self::ZIP_PATH . '" ';
        $command .= sprintf(self::ZIP_QUERY_EXTRACT, $archivePath, $targetDirectory);
        exec($command);
        return true;
    }
    
    /**
     * @param mixed $directoryPath
     * @param mixed $archiveName
     * @param mixed $checkUpdate
     * @return bool
     * @throws Exception
     */
    public static function archivePath($directoryPath, $archiveName, $checkUpdate = false) {
        $directoryPath = realpath($directoryPath);
        if ($directoryPath === false) {
            return false;
        }
        // $rootPath = realpath($directoryPath . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
        // $dirPath = substr($directoryPath, strlen($rootPath));
        
        if (! is_readable(self::ZIP_PATH)) {
            throw new Exception('7-Zip not found @ ' . self::ZIP_PATH);
        }
        $modifyTimes = self::dirModifyTime($directoryPath);
        if ($modifyTimes === false) {
            return false;
        }
        if (is_file($archiveName) and max($modifyTimes) < self::changetime($archiveName)) {
            return false;
        }
        $command = '"' . self::ZIP_PATH . '" ';
        $command .= sprintf(self::ZIP_QUERY_UPDATE, $archiveName, $directoryPath);
        exec($command);
        return true;
    }
    
    /**
     * @param mixed $destPath
     * @param mixed $sourceURI
     * @param array<mixed> $options
     * @return string
     * @throws Exception
     */
    public static function downloadByURI($destPath, $sourceURI, array $options = []) {
        $ret = 'ERROR';
        $downloadCommand = $options['download-cmd'] ?? 'curl %s -o %s';
        $copyCommand = $options['copy-cmd'] ?? 'copy %s %s /y';
        $successCommand = $options['success-cmd'] ?? null;
        $successPHP = $options['success-php'] ?? null;
        
        // $tempPath = tempnam(sys_get_temp_dir(), 'FS');
        $tempPath = temp_file(__CLASS__);
        $downloadExec = sprintf($downloadCommand, escapeshellarg($sourceURI), escapeshellarg($tempPath));
        // my_dump($downloadExec);
        exec($downloadExec);
        if (file_exists($tempPath)) {
            if (self::size($tempPath)) {
                $download = true;
                if (file_exists($destPath)) {
                    if (md5_file($tempPath) === md5_file($destPath)) {
                        $ret = sprintf('File "%s" is already up to date!', $destPath);
                        $download = false;
                    }
                }
                if ($download) {
                    $copyExec = sprintf($copyCommand, escapeshellarg($tempPath), escapeshellarg($destPath));
                    exec($copyExec);
                    if (file_exists($destPath)) {
                        $ret = sprintf('Updated file "%s"!', $destPath);
                        if ($successCommand) {
                            $successExec = sprintf($successCommand, escapeshellarg($destPath));
                            exec($successExec);
                        }
                        if ($successPHP) {
                            eval($successPHP);
                        }
                    }
                }
            }
        }
        return $ret;
    }
    
    /**
     * @param mixed $sourceURI
     * @param mixed $linkQuery
     * @return string|false
     */
    public static function getLinkByXPath($sourceURI, $linkQuery) {
        $ret = false;
        $sourceParam = parse_url($sourceURI);
        $doc = new DOMDocument();
        if (@$doc->loadHTMLFile($sourceURI)) {
            $xpath = new DOMXPath($doc);
            if ($uri = $xpath->evaluate(sprintf('string(%s)', $linkQuery))) {
                if (strpos($uri, '://') === false) {
                    if (strpos($uri, './') === 0) {
                        $uri = substr($uri, 2);
                    }
                    $uri = sprintf('%s://%s%s%s', $sourceParam['scheme'] ?? 'http', $sourceParam['host'] ?? '', $sourceParam['path'] ?? '', $uri);
                }
                $ret = $uri;
            }
        }
        return $ret;
    }
    
    /** @return list<list<string|null>|null>|null */
    public static function loadCSV(string $path, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): ?array {
        $ret = null;
        if ($handle = fopen($path, 'r')) {
            $ret = [];
            while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
                $ret[] = $row;
            }
            fclose($handle);
        }
        return $ret;
    }
    
    /** @var array<mixed> */
    private static array $base64Source = [
        '+',
        '/',
        '='
    ];
    
    /** @var array<mixed> */
    private static array $base64Target = [
        '-',
        '_',
        '~'
    ];
    
    /** Creates a filesystem-compatible name using base64. */
    public static function base64Encode(string $fileName): string {
        $fileName = base64_encode($fileName);
        $fileName = str_replace(self::$base64Source, self::$base64Target, $fileName);
        return $fileName;
    }
    
    /** Decodes a name created by base64Encode. */
    public static function base64Decode(string $fileName): string {
        $fileName = str_replace(self::$base64Target, self::$base64Source, $fileName);
        $fileName = base64_decode($fileName);
        return $fileName;
    }
    
    /** Deletes the contents of a directory. */
    public static function removeDir(string $path, bool $keepRoot = false): void {
        if ($path === '' or ! is_dir($path) or ! is_writable($path) or ! realpath($path)) {
            return;
        }
        
        if (is_link($path)) {
            // never delete contents of symlink targets
            if (! $keepRoot) {
                unlink($path);
            }
        } else {
            foreach (self::scanDir($path, FileSystem::SCANDIR_REALPATH) as $entry) {
                $file = (string) $entry;
                if (is_writable($file)) {
                    if (is_dir($file)) {
                        self::removeDir($file);
                    } else {
                        unlink($file);
                    }
                } else {
                    $keepRoot = true;
                }
            }
            
            if (! $keepRoot) {
                rmdir($path);
            }
        }
    }
    
    /**
     * Copies files and directories.
     *
     * @throws InvalidArgumentException
     * @throws DirectoryCouldNotBeCreatedException
     */
    public static function copy(string $from, string $to): void {
        if (($resolvedFrom = realpath($from)) === false) {
            throw new InvalidArgumentException("Source '$from' does not exist.");
        }
        
        $from = $resolvedFrom;
        
        if (is_dir($from)) {
            if (is_file($to)) {
                throw new InvalidArgumentException("Target '$to' is not a directory, but source '$from' is.");
            }
            
            if (! file_exists($to)) {
                mkdir($to, 0777, true);
            }
            
            if (realpath($to) === false) {
                throw new DirectoryCouldNotBeCreatedException("Target directory '$to' could not be created.");
            }
            
            $to = realpath($to);
            
            foreach (self::scanDir($from) as $file) {
                self::copy($from . DIRECTORY_SEPARATOR . $file, $to . DIRECTORY_SEPARATOR . $file);
            }
        } else {
            copy($from, $to);
            
            if (PHP_OS_FAMILY === 'Linux') {
                chmod($to, 0777);
            }
        }
    }
    
    /** Determines whether a command is available on the command line. */
    public static function commandExists(string $command): bool {
        $which = PHP_OS === 'WINNT' ? "where $command 2>NUL" : "command -v $command 2>/dev/null";
        return exec($which) !== '';
    }
}
