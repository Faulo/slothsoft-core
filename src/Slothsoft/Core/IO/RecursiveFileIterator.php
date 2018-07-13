<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;

class RecursiveFileIterator 
{
    public static function iterateDirectoriesAndFiles(string $directory) : iterable {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException("Directory '$directory' does not exist.");
        }
        $directory = realpath($directory);
        $iterator = new RecursiveDirectoryIterator($directory);
        $iterator = new class($iterator) extends RecursiveFilterIterator {
            public function accept() : bool {
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
    public static function iterateFiles(string $directory) : iterable {
        foreach (self::iterateDirectoriesAndFiles($directory) as $file) {
            if (is_file($file)) {
                yield $file;
            }
        }
    }
    public static function iterateDirectories(string $directory) : iterable {
        foreach (self::iterateDirectoriesAndFiles($directory) as $file) {
            if (is_dir($file)) {
                yield $file;
            }
        }
    }
}

