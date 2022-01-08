<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

class StringWriterFromFileWriter implements StringWriterInterface {

    /** @var FileWriterInterface */
    private $source;

    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }

    public function toString(): string {
        return file_get_contents((string) $this->source->toFile());
    }
}

