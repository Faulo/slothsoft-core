<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Generator;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

final class ChunkWriterFromGenerator implements ChunkWriterInterface {
    
    private Generator $generator;
    
    /**
     * @param Generator $generator
     * @return void
     */
    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }
    
    /**
     * @return Generator
     */
    public function toChunks(): Generator {
        return $this->generator;
    }
}

