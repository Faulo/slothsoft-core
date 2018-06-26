<?php
namespace Slothsoft\Core\IO;

use DOMDocument;
use SplFileInfo;

class FileInfoFactory
{
    public static function createTempFile() : SplFileInfo {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . __CLASS__;
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return self::createFromPath($path . DIRECTORY_SEPARATOR . uniqid());
    }
    
    public static function createFromPath(string $path) : SplFileInfo {
        return new SplFileInfo($path);
    }
    public static function createFromString(string $data) : SplFileInfo {
        $file = self::createTempFile();
        file_put_contents((string) $file, $data);
        return $file;
    }
    public static function createFromDocument(DOMDocument $document) : SplFileInfo {
        $file = self::createTempFile();
        $document->save((string) $file);
        return $file;
    }
    public static function createFromResource($resource) : SplFileInfo
    {
        $file = self::createTempFile();
        file_put_contents((string) $file, $resource);
        return $file;
    }
}

