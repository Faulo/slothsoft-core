<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromChunkWriter implements StringWriterInterface {
    
    private ChunkWriterInterface $source;
    
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
    private ?string $result = null;
    
    public function toString(): string {
        if ($this->result === null) {
            $this->result = '';
            foreach ($this->source->toChunks() as $data) {
                $this->result .= $data;
            }
        }
        
        return $this->result;
    }
}

