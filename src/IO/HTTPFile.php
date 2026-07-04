<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use DOMDocument;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\Storage;
use SplFileInfo;

/**
 * Legacy HTTP-backed file writer.
 *
 * @author Daniel Schulz
 * @since 2014-05-28
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
final class HTTPFile implements FileWriterInterface {
    
    const STATUS_BAD_REQUEST = 400;
    
    const CURL_ENABLED = true;
    
    const CURL_COMMAND = 'curl %s --output %s --header %s --connect-timeout 300 --retry 3 --http1.1 --silent --fail --insecure --location';
    
    /**
     *
     * @return string
     */
    public static function getTempFile(): string {
        // $ret = tempnam(sys_get_temp_dir() . DIRECTORY_SEPARATOR . __NAMESPACE__, __CLASS__);
        $ret = temp_file(__CLASS__);
        // my_dump($ret);
        return $ret;
    }
    
    /**
     *
     * @param string $filePath
     * @param string $fileName
     * @return HTTPFile
     */
    public static function createFromPath(string $filePath, string $fileName = ''): HTTPFile {
        return new HTTPFile($filePath, $fileName);
    }
    
    public static function createFromTemp(string $fileName = ''): HTTPFile {
        return self::createFromPath(self::getTempFile(), $fileName);
    }
    
    /**
     *
     * @param DOMDocument $doc
     * @param string $fileName
     * @return HTTPFile
     */
    public static function createFromDocument(DOMDocument $doc, string $fileName = ''): HTTPFile {
        if ($fileName === '') {
            $fileName = 'index.xml';
        }
        $file = self::createFromTemp($fileName);
        $file->setDocument($doc);
        return $file;
    }
    
    /**
     *
     * @param string $content
     * @param string $fileName
     * @return HTTPFile
     */
    public static function createFromString(string $content, string $fileName = ''): HTTPFile {
        $fileName = (string) $fileName;
        if ($fileName === '') {
            $fileName = 'index.txt';
        }
        $file = self::createFromTemp($fileName);
        $file->setContents($content);
        return $file;
    }
    
    /**
     *
     * @param array $content
     * @param string $fileName
     * @return HTTPFile
     */
    public static function createFromFileList(array $fileList, string $fileName = ''): HTTPFile {
        $fileName = (string) $fileName;
        if ($fileName === '') {
            $fileName = 'index.txt';
        }
        $content = '';
        foreach ($fileList as $file) {
            $content .= $file->getContents();
        }
        return self::createFromString($content, $fileName);
    }
    
    /**
     *
     * @param resource $resource
     * @param string $fileName
     * @return HTTPFile
     */
    public static function createFromStream($resource, string $fileName = ''): HTTPFile {
        $fileName = (string) $fileName;
        if ($fileName === '') {
            $fileName = 'index.txt';
        }
        $file = self::createFromTemp($fileName);
        $file->setStream($resource);
        return $file;
    }
    
    /**
     *
     * @param mixed $object
     * @param string $fileName
     * @return HTTPFile
     */
    public static function createFromJSON($object, string $fileName = ''): HTTPFile {
        $fileName = (string) $fileName;
        if ($fileName === '') {
            $fileName = 'data.json';
        }
        return self::createFromString(json_encode($object), $fileName);
    }
    
    /**
     *
     * @param string $phpCommand
     * @param string $fileName
     * @return NULL|HTTPFile
     */
    public static function createFromPHP(string $phpCommand, string $fileName = ''): ?HTTPFile {
        if ($fileName === '') {
            $fileName = basename($phpCommand);
            if ($fileName === '') {
                $fileName = 'data.bin';
            }
        }
        $file = self::createFromTemp($fileName);
        $exec = sprintf('php %s > %s', escapeshellarg($phpCommand), escapeshellarg($file->getPath()));
        exec($exec);
        return $file;
    }
    
    /**
     *
     * @param string $url
     * @param string $fileName
     * @return NULL|HTTPFile
     */
    public static function createFromURL(string $url, string $fileName = ''): ?HTTPFile {
        if ($fileName === '') {
            $fileName = basename($url);
            if ($fileName === '') {
                $fileName = 'data.bin';
            }
        }
        $param = parse_url($url);
        if (! isset($param['host'])) {
            $url = 'http://slothsoft.net' . $url;
        }
        
        if (self::CURL_ENABLED) {
            $refererURI = sprintf('Referer: %s://%s%s', $param['scheme'], $param['host'], $param['path']);
            $filePath = self::getTempFile();
            $downloadExec = sprintf(self::CURL_COMMAND, escapeshellarg(urldecode($url)), escapeshellarg($filePath), escapeshellarg($refererURI));
            shell_exec($downloadExec);
            $ret = file_exists($filePath) ? self::createFromPath($filePath, $fileName) : null;
        } else {
            @$data = file_get_contents($url);
            $ret = strlen($data) ? self::createFromString($data, $fileName) : null;
        }
        return $ret;
    }
    
    /**
     *
     * @param string $filePath
     * @param string $url
     * @param int $headerCache
     * @return NULL|HTTPFile
     */
    public static function createFromDownload(string $filePath, string $url, int $headerCache = Seconds::YEAR): ?HTTPFile {
        $ret = self::verifyDownload($filePath, $url, $headerCache);
        if (! $ret) {
            if ($file = self::createFromURL($url)) {
                $ret = $file->copyTo(dirname($filePath), basename($filePath));
            }
        }
        return $ret ? self::createFromPath($filePath) : null;
    }
    
    /**
     *
     * @param string $url
     * @param int $headerCache
     * @return boolean
     */
    public static function verifyURL(string $url, int $headerCache = Seconds::YEAR): bool {
        $ret = false;
        if ($res = Storage::loadExternalHeader($url, $headerCache)) {
            $status = isset($res['status']) ? (int) $res['status'] : self::STATUS_BAD_REQUEST;
            if ($status < self::STATUS_BAD_REQUEST) {
                $length = isset($res['content-length']) ? (string) $res['content-length'] : '0';
                if ($length !== '0') {
                    $ret = true;
                }
            }
        }
        return $ret;
    }
    
    /**
     *
     * @param string $filePath
     * @param string $url
     * @param int $headerCache
     * @return boolean
     */
    public static function verifyDownload(string $filePath, string $url, int $headerCache = Seconds::YEAR): bool {
        $ret = false;
        if (file_exists($filePath)) {
            if ($headerCache === -1) {
                $ret = true;
            } else {
                $res = Storage::loadExternalHeader($url, $headerCache);
                $sizeA = isset($res['content-length']) ? (string) $res['content-length'] : '0';
                $sizeB = (string) filesize($filePath);
                if ($sizeA === $sizeB) {
                    $ret = true;
                }
            }
        }
        return $ret;
    }
    
    private $path;
    
    private $name;
    
    private function __construct(string $filePath, string $fileName = '') {
        if ($fileName === '') {
            $fileName = basename($filePath);
        }
        $this->path = $filePath;
        $this->name = $fileName;
    }
    
    public function getPath(): string {
        return $this->path;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getContents(): string {
        return file_get_contents($this->getPath());
    }
    
    public function setContents(string $content) {
        return file_put_contents($this->getPath(), $content);
    }
    
    public function setStream($content) {
        return file_put_contents($this->getPath(), $content);
    }
    
    public function getDocument(): DOMDocument {
        return DOMHelper::loadDocument($this->getPath());
    }
    
    public function setDocument(DOMDocument $content) {
        return $content->save($this->getPath());
    }
    
    public function copyTo($dir, $name = null, $copyClosure = null) {
        $ret = false;
        if ($dir = realpath($dir)) {
            if (! $name) {
                $name = $this->name;
            }
            $sourcePath = $this->path;
            $targetPath = $dir . DIRECTORY_SEPARATOR . $name;
            if (is_callable($copyClosure)) {
                $ret = $copyClosure($sourcePath, $targetPath);
            } elseif (is_string($copyClosure) and strlen($copyClosure)) {
                $command = sprintf($copyClosure, escapeshellarg($sourcePath), escapeshellarg($targetPath));
                $output = [];
                $result = 0;
                exec($command, $output, $result);
                $ret = ($result === 0);
            } else {
                $ret = copy($sourcePath, $targetPath);
            }
        }
        return $ret;
    }
    
    public function delete(): bool {
        return unlink($this->getPath());
    }
    
    public function exists(): bool {
        return is_file($this->path);
    }
    
    public function toFile(): SplFileInfo {
        return FileInfoFactory::createFromPath($this->path);
    }
    
    public function toString(): string {
        return $this->getContents();
    }
    
    public function toFileName(): string {
        return $this->getName();
    }
}
