<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Psr7\LazyStringWriterStream;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StreamWriterFromStringWriter implements StreamWriterInterface {
    
    private StringWriterInterface $source;
    
    /**
     * @param StringWriterInterface $source
     * @return void
     */
    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return StreamInterface
     */
    public function toStream(): StreamInterface {
        return new LazyStringWriterStream($this->source);
    }
}

