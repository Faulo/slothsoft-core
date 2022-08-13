<?php
declare(strict_types = 1);
/**
 * *********************************************************************
 * FileSystem v1.00 19.10.2012 © Daniel Schulz
 *
 * Changelog:
 * v1.00 19.10.2012
 * initial release
 * *********************************************************************
 */
namespace Slothsoft\Core;

use Slothsoft\Core\Calendar\DateTimeFormatter;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
use SplFileInfo;
use com;
use finfo;

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

    public static $sizeUnits = [
        'B',
        'kB',
        'MB',
        'GB',
        'TB'
    ];

    public static $videoExtensions = [
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

    public static $audioExtensions = [
        'mp3',
        'wav',
        'ogg',
        'wma',
        'ogm',
        'mid',
        'm4a'
    ];

    public static $subttitleExtensions = [
        'vtt',
        'srt',
        'ssa',
        'ass'
    ];

    public static function isVideo(string $fileName): bool {
        return in_array(strtolower(self::extension($fileName)), self::$videoExtensions);
    }

    public static function isAudio(string $fileName): bool {
        return in_array(strtolower(self::extension($fileName)), self::$audioExtensions);
    }

    public static function isSubttitle(string $fileName): bool {
        return in_array(strtolower(self::extension($fileName)), self::$subttitleExtensions);
    }

    public static function drawBytes(int $size, int $precision = 2): string {
        for ($i = 0; $size > 1024; $i ++) {
            $size /= 1024.0;
        }
        return sprintf('%.' . $precision . 'f %s', $size, self::$sizeUnits[$i]);
    }

    public static function getStorage(): Storage {
        static $storage = null;
        if (! $storage) {
            $storage = new Storage('FileSystem');
        }
        return $storage;
    }

    public static function generateStorageKey(string $path, string $hash = ''): string {
        $key = realpath($path);
        if (! $key) {
            throw new Exception(sprintf('PATH NOT FOUND: "%s"', $path));
        }
        if (strlen($hash)) {
            $key .= '#' . $hash;
        }
        return sprintf('FileSystem://%s', $key);
    }

    public static function asNode(string $path, DOMDocument $dataDoc = null): ?DOMNode {
        $retNode = null;
        $storage = null;
        $returnDocument = ! $dataDoc;
        if ($path = realpath($path)) {
            $name = basename($path);
            if ($name === 'Thumbs.db') {
                @unlink($path);
            } else {
                if (is_dir($path)) {
                    $modifyTime = max(self::dirModifyTime($path));
                    $storage = self::getStorage();
                    $storageKey = self::generateStorageKey($path);
                    if ($tmpNode = $storage->retrieveXML($storageKey, $modifyTime, $dataDoc)) {
                        if (! $dataDoc) {
                            $dataDoc = $tmpNode->ownerDocument;
                        }
                        if ($tmpNode->firstChild instanceof DOMElement) {
                            $retNode = $tmpNode->removeChild($tmpNode->firstChild);
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
                        $attr['size-string'] = self::drawBytes($attr['size-int']);
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
                            if ($node = self::asNode($child, $dataDoc)) {
                                $attr['size-int'] += (float) $node->getAttribute('size-int');
                                $childNodes[] = $node;
                            }
                        }
                        $attr['size-string'] = self::drawBytes($attr['size-int']);
                    }
                    if ($tagName) {
                        $retNode = $dataDoc->createElement($tagName);
                        $time = self::changetime($path);
                        $attr['change-datetime'] = date(DateTimeFormatter::FORMAT_DATETIME, $time);
                        $attr['change-utc'] = date(DateTimeFormatter::FORMAT_UTC, $time);
                        $time = self::maketime($path);
                        $attr['make-datetime'] = date(DateTimeFormatter::FORMAT_DATETIME, $time);
                        $attr['make-utc'] = date(DateTimeFormatter::FORMAT_UTC, $time);
                        $attr['path'] = $path;
                        $attr['id'] = 'id-' . md5($path);
                        $attr['name'] = $name;
                        $attr['title'] = isset($attr['ext']) ? substr($attr['name'], 0, - (1 + strlen($attr['ext']))) : $attr['name'];
                        foreach ($attr as $key => $val) {
                            $retNode->setAttribute($key, utf8_encode((string) $val));
                        }
                        $retNode->setIdAttribute('id', true);
                        foreach ($childNodes as $node) {
                            $retNode->appendChild($node);
                        }
                        if ($storage) {
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
        $size = null;
        if (is_readable($fileName)) {
            $size = filesize($fileName);
        } elseif ($file = self::lookupFile($fileName)) {
            $size = $file->Size;
        }
        return $size;
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
        return $time;
    }

    public static function maketime(string $fileName): ?int {
        // $time = filemtime($fileName);
        $time = null;
        if (is_readable($fileName)) {
            $time = filectime($fileName);
        } elseif ($file = self::lookupFile($fileName)) {
            $time = strtotime($file->DateLastModified);
        }
        return $time;
    }

    public static function lookupFile(string $fileName) {
        if (is_readable($fileName)) {
            $com = new COM('Scripting.FileSystemObject');
            return is_file($fileName) ? $com->GetFile($fileName) : $com->GetFolder($fileName);
        } elseif ($folder = self::lookupFile(dirname($fileName))) {
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
        $filename = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~iu', '$1', $filename);
        $filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');
        $filename = mb_convert_encoding($filename, 'UTF-8', 'UTF-8');
        $filename = str_replace($notAllowed, $toReplace, $filename);
        $filename = preg_replace('~\s+~iu', ' ', $filename);
        $filename = trim($filename);
        return $filename;
    }

    public static function download($filePath, $downloadName) {
        // error_reporting(0);
        $size = (float) self::size($filePath);
        $start = 0.0;
        $end = $size;
        $headers = [
            'HTTP/1.1 200 OK',
            'Connection: close',
            'Content-Description: File Transfer',
            'Content-Type: application/octet-stream',
            'Content-Transfer-Encoding: binary',
            'Accept-Ranges: bytes',
            'Cache-Control: must-revalidate',
            'Transfer-Encoding: chunked'
        ];
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            $match = [];
            if (preg_match('/^bytes=(\d+)-$/', $range, $match)) {
                $start = (float) $match[1];
                // file_put_contents('start.'.time() . '.1.json', json_encode($start));
            } elseif (preg_match('/^bytes=(\d+)-(\d+)$/', $range, $match)) {
                $start = (float) $match[1];
                $end = (float) $match[2];
                file_put_contents('ENDBYTE.' . time() . '.txt', $range);
            }
        }
        $length = $end - $start;
        if ($start or $end !== $size) {
            $headers[0] = 'HTTP/1.1 206 Partial content';
            $headers[] = sprintf('Content-Range: bytes %1$.0f-%2$.0f/%3$.0f', $start, $end - 1, $size);
        }
        $headers[] = sprintf('Content-Disposition: attachment; filename*=UTF-8\'\'%1$s', rawurlencode($downloadName));
        $headers[] = sprintf('Content-Length: %1$.0f', $length);
        foreach ($headers as $head) {
            header($head);
        }
        self::outputChunk($filePath, $start, $length);
    }

    public static function outputChunk($filePath, $start, $length) {
        set_time_limit(0);
        // how many bytes per chunk
        $chunksize = 16384; // 1 kB
        $chunksize = 1073741824; // 1 GB
        $chunksize = 16777216; // 16 MB
        $chunksize = 8192; // 8 kB
        $chunksize = 1048576; // 1 MB
        $handle = fopen($filePath, 'rb');
        if ($start < PHP_INT_MAX) {
            fseek($handle, $start, SEEK_SET);
        } else {
            /*
             * fseek($handle, PHP_INT_MAX, SEEK_SET);
             * $start -= PHP_INT_MAX;
             * while ($start > 0) {
             * fread($handle, min($chunksize, $start));
             * $start -= $chunksize;
             * }
             * //
             */
            while ($start > 0) {
                fread($handle, min($chunksize, $start));
                $start -= $chunksize;
            }
        }
        // *
        while ($length > 0) {
            $read = min($chunksize, $length);
            echo dechex($read) . "
";
            echo fread($handle, $read) . "
";
            flush();
            $length -= $chunksize;
        }
        echo "0

";
        // */
        // fpassthru($handle);
        fclose($handle);
    }

    /**
     * List the contents of a directory.
     *
     * @param string $relPath
     * @param int $options
     * @param string $filter
     * @return array
     */
    public static function scanDir(string $relPath, int $options = 0, ?string $filter = null): array {
        $dirPath = realpath((string) $relPath);
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
                $tmp[$val] = preg_replace('/\.[^\.]+$/', '', $val);
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

    public static function dirModifyTime(string $dirPath, ?string $rootPath = null) {
        if ($rootPath === null) {
            $dirPath = realpath($dirPath);
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
            $modifyTime = self::changetime($filePath);
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
                        if (! isset($ret[$type])) {
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

    public static function archivePath($directoryPath, $archiveName, $checkUpdate = false) {
        $directoryPath = realpath($directoryPath);
        // $rootPath = realpath($directoryPath . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
        // $dirPath = substr($directoryPath, strlen($rootPath));

        if (! is_readable(self::ZIP_PATH)) {
            throw new Exception('7-Zip not found @ ' . self::ZIP_PATH);
        }
        if (is_file($archiveName) and max(self::dirModifyTime($directoryPath)) < self::changetime($archiveName)) {
            return false;
        }
        $command = '"' . self::ZIP_PATH . '" ';
        $command .= sprintf(self::ZIP_QUERY_UPDATE, $archiveName, $directoryPath);
        exec($command);
        return true;
    }

    private static function add2archive($path, $archive, $rootPath, &$fileCount = 0) {
        $ret = true;
        if (is_dir($rootPath . $path)) {
            $archive->addEmptyDir($path);
            $arr = self::scanDir($rootPath . $path);
            foreach ($arr as $childPath) {
                $ret = ($ret and self::add2archive($path . DIRECTORY_SEPARATOR . $childPath, $archive, $rootPath, $fileCount));
            }
        }
        if (is_file($rootPath . $path)) {
            $fileCount ++;
            if ($fileCount > self::ARCHIVE_MAX_FILES) {
                $fileCount = 0;
                $name = $archive->filename;
                $ret = ($ret and $archive->close() and $archive->open($name));
            }
            if ($ret) {
                $archive->addFile($rootPath . $path, $path, $fileCount);
            }
        }
        return $ret;
    }

    public static function downloadByURI($destPath, $sourceURI, array $options = []) {
        $ret = 'ERROR';
        $downloadCommand = isset($options['download-cmd']) ? $options['download-cmd'] : 'curl %s -o %s';
        $copyCommand = isset($options['copy-cmd']) ? $options['copy-cmd'] : 'copy %s %s /y';
        $successCommand = isset($options['success-cmd']) ? $options['success-cmd'] : null;
        $successPHP = isset($options['success-php']) ? $options['success-php'] : null;

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
                    $uri = sprintf('%s://%s%s%s', $sourceParam['scheme'], $sourceParam['host'], $sourceParam['path'], $uri);
                }
                $ret = $uri;
            }
        }
        return $ret;
    }

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

    private static $base64Source = [
        '+',
        '/',
        '='
    ];

    private static $base64Target = [
        '-',
        '_',
        '~'
    ];

    /**
     * Creates a filesystem-compatible name using base64.
     *
     * @param string $fileName
     * @return string
     */
    public static function base64Encode(string $fileName): string {
        $fileName = base64_encode($fileName);
        $fileName = str_replace(self::$base64Source, self::$base64Target, $fileName);
        return $fileName;
    }

    /**
     * Decodes a name created by base64Encode.
     *
     * @param string $fileName
     * @return string
     */
    public static function base64Decode(string $fileName): string {
        $fileName = str_replace(self::$base64Target, self::$base64Source, $fileName);
        $fileName = base64_decode($fileName);
        return $fileName;
    }

    /**
     * Deletes the contents of a directory.
     *
     * @param string $path
     * @param bool $keepRoot
     */
    public static function removeDir(string $path, bool $keepRoot = false): void {
        if (! is_dir($path)) {
            return;
        }
        foreach (self::scanDir($path, FileSystem::SCANDIR_REALPATH) as $file) {
            if (is_dir($file)) {
                self::removeDir($file);
            } else {
                unlink($file);
            }
        }
        if (! $keepRoot) {
            rmdir($path);
        }
    }

    /**
     * Copies files and directories.
     *
     * @param string $from
     * @param string $to
     */
    public static function copy(string $from, string $to): void {
        assert(file_exists($from));

        $from = realpath($from);

        if (is_dir($from)) {
            if (! file_exists($to)) {
                mkdir($to, 0777, true);
            }
            $to = realpath($to);

            assert(is_dir($to));

            foreach (self::scanDir($from) as $file) {
                self::copy($from . DIRECTORY_SEPARATOR . $file, $to . DIRECTORY_SEPARATOR . $file);
            }
        } else {
            copy($from, $to);
        }
    }

    /**
     * Determines whether or not a command is available on the command line.
     *
     * @param string $command
     * @return bool
     */
    public static function commandExists(string $command): bool {
        $which = PHP_OS === 'WINNT' ? "where $command 2>NUL" : "command -v $command 2>/dev/null";
        return exec($which) !== '';
    }
}