<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;

final class RecursiveFileIterator {
    
    /**
     * @param string $directory
     * @return iterable
     * @throws InvalidArgumentException
     */
    public static function iterateDirectoriesAndFiles(string $directory): iterable {
        if (! is_dir($directory)) {
            throw new InvalidArgumentException("Directory '$directory' does not exist.");
        }
        $directory = realpath($directory);
        $iterator = new RecursiveDirectoryIterator($directory);
        $iterator = new class($iterator) extends RecursiveFilterIterator {
            
            /**
             * @return bool
             */
            public function accept(): bool {
                $name = $this->current()->getFilename();
                return $name !== '..';
            }
        };
        $iterator = new RecursiveIteratorIterator($iterator);
        foreach ($iterator as $file) {
            $path = $file->getRealPath();
            if ($path !== $directory) {
                yield $path;
            }
        }
    }
    
    /**
     * @param string $directory
     * @return iterable
     */
    public static function iterateFiles(string $directory): iterable {
        foreach (self::iterateDirectoriesAndFiles($directory) as $file) {
            if (is_file($file)) {
                yield $file;
            }
        }
    }
    
    /**
     * @param string $directory
     * @return iterable
     */
    public static function iterateDirectories(string $directory): iterable {
        foreach (self::iterateDirectoriesAndFiles($directory) as $file) {
            if (is_dir($file)) {
                yield $file;
            }
        }
    }
}
