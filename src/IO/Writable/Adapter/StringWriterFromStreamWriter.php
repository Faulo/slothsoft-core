<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromStreamWriter implements StringWriterInterface {
    
    private StreamWriterInterface $source;
    
    public function __construct(StreamWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toString(): string {
        return (string) $this->source->toStream();
    }
}

