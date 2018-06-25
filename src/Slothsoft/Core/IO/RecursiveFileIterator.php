<?php
namespace Slothsoft\Core\IO;

use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;

class RecursiveFileIterator extends RecursiveFilterIterator
{
    public static function iterateDirectory(string $directory) : RecursiveIteratorIterator {
        $directoryIterator = new RecursiveDirectoryIterator($directory);
        $filteredIterator = new self($directoryIterator);
        return new RecursiveIteratorIterator($filteredIterator);
    }
    public function accept() : bool {
        return $this->current()->getFilename()[0] !== '.';
    }
}

