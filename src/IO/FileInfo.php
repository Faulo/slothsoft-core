<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use InvalidArgumentException;
use RuntimeException;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use SplFileInfo;

final class FileInfo extends SplFileInfo implements FileWriterInterface, StringWriterInterface {
    
    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function readContents(string $path): string {
        if (is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Unable to read contents of directory "%s".', $path));
        }
        $result = @file_get_contents($path);
        if ($result === false) {
            throw new RuntimeException(sprintf('Unable to read contents of "%s".', $path));
        }
        return $result;
    }
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        return $this;
    }
    
    /**
     * @return string
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function toString(): string {
        return self::readContents($this->getPathname());
    }
}
