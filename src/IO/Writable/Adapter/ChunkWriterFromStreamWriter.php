<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Generator;
use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

final class ChunkWriterFromStreamWriter implements ChunkWriterInterface {
    
    private StreamWriterInterface $source;
    
    private int $chunkSize;
    
    public function __construct(StreamWriterInterface $source, int $chunkSize = Memory::ONE_KILOBYTE) {
        $this->source = $source;
        $this->chunkSize = $chunkSize;
    }
    
    public function toChunks(): Generator {
        $handle = $this->source->toStream();
        while (! $handle->eof()) {
            yield $handle->read($this->chunkSize);
        }
        $handle->close();
    }
}

