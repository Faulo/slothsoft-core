<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use SplFileInfo;

final class FileInfo extends SplFileInfo implements FileWriterInterface, StringWriterInterface {
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        return $this;
    }
    
    /**
     * @return string
     */
    public function toString(): string {
        return file_get_contents((string) $this);
    }
}
