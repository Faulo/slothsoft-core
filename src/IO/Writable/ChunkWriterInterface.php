<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Generator;

interface ChunkWriterInterface {
    
    /**
     * Converts the object's held data to a Generator.
     * Subsequent calls are expected to create a new Generator object each time.
     *
     * @return Generator
     */
    public function toChunks(): Generator;
}

