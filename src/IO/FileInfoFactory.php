<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use DOMDocument;
use InvalidArgumentException;
use Slothsoft\Core\FileSystem;

final class FileInfoFactory {
    
    /**
     * @return FileInfo
     */
    public static function createTempFile(): FileInfo {
        return self::createFromPath(temp_file(__CLASS__));
    }
    
    /**
     * @param string $path
     * @return FileInfo
     */
    public static function createFromPath(string $path): FileInfo {
        return new FileInfo($path);
    }
    
    /**
     * @param string $path
     * @return FileInfo
     */
    public static function createDirectoryFromPath(string $path): FileInfo {
        FileSystem::ensureDirectory($path);
        return self::createFromPath($path);
    }
    
    /**
     * @param string $data
     * @return FileInfo
     */
    public static function createFromString(string $data): FileInfo {
        $file = self::createTempFile();
        file_put_contents((string) $file, $data);
        return $file;
    }
    
    /**
     * @param DOMDocument $document
     * @return FileInfo
     */
    public static function createFromDocument(DOMDocument $document): FileInfo {
        $file = self::createTempFile();
        $document->save((string) $file);
        return $file;
    }
    
    /**
     * @param mixed $resource
     * @return FileInfo
     */
    public static function createFromResource($resource): FileInfo {
        $file = self::createTempFile();
        file_put_contents((string) $file, $resource);
        return $file;
    }
    
    /**
     * @param string $tmpName
     * @return FileInfo
     * @throws InvalidArgumentException
     */
    public static function createFromUpload(string $tmpName): FileInfo {
        if (! is_uploaded_file($tmpName)) {
            throw new InvalidArgumentException("File 'tmpName' was not uploaded.");
        }
        return self::createFromPath($tmpName);
    }
}
