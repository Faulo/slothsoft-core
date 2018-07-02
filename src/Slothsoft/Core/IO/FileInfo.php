<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

class FileInfo extends SplFileInfo implements FileWriterInterface 
{
    public function toFile(): SplFileInfo
    {
        return $this;
    }
    
    public function toFileName(): string
    {
        return $this->getFilename();
    }

    public function toString(): string
    {
        return file_get_contents((string) $this);
    }
}

