<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Psr7\OneTimeGeneratorStream;
use Slothsoft\Core\IO\Psr7\PersistentGeneratorStream;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

final class StreamWriterFromChunkWriter implements StreamWriterInterface {
    
    private ChunkWriterInterface $source;
    
    private bool $canLoadAllChunks;
    
    public function __construct(ChunkWriterInterface $source, bool $canLoadAllChunks = true) {
        $this->source = $source;
        $this->canLoadAllChunks = $canLoadAllChunks;
    }
    
    public function toStream(): StreamInterface {
        return $this->canLoadAllChunks ? new PersistentGeneratorStream($this->source) : new OneTimeGeneratorStream($this->source);
    }
}

