<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Psr7\GeneratorStream;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

class StreamWriterFromChunkWriter implements StreamWriterInterface {
    
    /** @var ChunkWriterInterface */
    private $source;
    
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toStream(): StreamInterface {
        return new GeneratorStream($this->source);
    }
}

