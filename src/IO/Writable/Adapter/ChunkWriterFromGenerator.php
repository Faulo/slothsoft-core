<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

final class ChunkWriterFromGenerator implements ChunkWriterInterface {
    
    private Generator $generator;
    
    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }
    
    public function toChunks(): Generator {
        return $this->generator;
    }
}

