<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Generator;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class ChunkWriterFromStringWriter implements ChunkWriterInterface {
    
    private StringWriterInterface $source;
    
    /**
     * @param StringWriterInterface $source
     * @return void
     */
    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return Generator
     */
    public function toChunks(): Generator {
        yield $this->source->toString();
    }
}

