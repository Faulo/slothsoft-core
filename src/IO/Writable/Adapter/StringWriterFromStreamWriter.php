<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

class StringWriterFromStreamWriter implements StringWriterInterface {

    private $source;

    public function __construct(StreamWriterInterface $source) {
        $this->source = $source;
    }

    public function toString(): string {
        return $this->source->toStream()->getContents();
    }
}

