<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromChunkWriter implements StringWriterInterface {
    
    private ChunkWriterInterface $source;
    
    /**
     * @param ChunkWriterInterface $source
     */
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return string
     */
    public function toString(): string {
        $result = '';
        /** @noinspection PhpLoopCanBeReplacedWithImplodeInspection */
        foreach ($this->source->toChunks() as $data) {
            $result .= $data;
        }
        
        return $result;
    }
}

