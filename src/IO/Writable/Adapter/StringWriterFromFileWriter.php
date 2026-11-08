<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromFileWriter implements StringWriterInterface {
    
    private FileWriterInterface $source;
    
    /**
     * @param FileWriterInterface $source
     */
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return string
     */
    public function toString(): string {
        $result = file_get_contents((string) $this->source->toFile());
        if ($result === false) {
            throw new \RuntimeException('Unable to read or serialize content.');
        }
        return $result;
    }
}

