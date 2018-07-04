<?php
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

class ChunkWriterMemoryCache implements ChunkWriterInterface
{
    private $source;
    private $result;
    
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }

    public function toChunks(): Generator
    {
        if ($this->result === null or !$this->result->valid()) {
            $this->result = $this->source->toChunks();
        }
        return $this->result;
    }
}

