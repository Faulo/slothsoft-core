<?php
namespace Slothsoft\Core\IO;

use DOMDocument;
use InvalidArgumentException;

class FileInfoFactory
{
    public static function createTempFile() : FileInfo {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . __CLASS__;
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return self::createFromPath($path . DIRECTORY_SEPARATOR . uniqid());
    }
    
    public static function createFromPath(string $path) : FileInfo {
        return new FileInfo($path);
    }
    public static function createFromString(string $data) : FileInfo {
        $file = self::createTempFile();
        file_put_contents((string) $file, $data);
        return $file;
    }
    public static function createFromDocument(DOMDocument $document) : FileInfo {
        $file = self::createTempFile();
        $document->save((string) $file);
        return $file;
    }
    public static function createFromResource($resource) : FileInfo
    {
        $file = self::createTempFile();
        file_put_contents((string) $file, $resource);
        return $file;
    }
    public static function createFromUpload(string $tmpName) : FileInfo {
        if (!is_uploaded_file($tmpName)) {
            throw new InvalidArgumentException("File 'tmpName' was not uploaded.");
        }
        return self::createFromPath($tmpName);
    }
}

