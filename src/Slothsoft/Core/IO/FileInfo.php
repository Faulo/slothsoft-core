<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

class FileInfo extends SplFileInfo implements FileWriterInterface, StringWriterInterface
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

