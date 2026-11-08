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
     * @throws \RuntimeException
     */
    public function toString(): string {
        $result = file_get_contents((string) $this);
        if ($result === false) {
            throw new \RuntimeException('Unable to read or serialize content.');
        }
        return $result;
    }
}
