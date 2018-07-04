<?php
namespace Slothsoft\Core\IO\Writable\Adapter;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\GeneratorStream;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

class StreamWriterFromChunkWriter implements StreamWriterInterface
{
    private $source;
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toStream(): StreamInterface
    {
        return new GeneratorStream($this->source);
    }

}

