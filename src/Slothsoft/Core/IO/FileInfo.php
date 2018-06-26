<?php
namespace Slothsoft\Core\IO;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

class FileInfo extends SplFileInfo implements FileWriterInterface 
{
    public function toFile(): SplFileInfo
    {
        return $this;
    }

    public function toString(): string
    {
        return file_get_contents((string) $this);
    }
}

