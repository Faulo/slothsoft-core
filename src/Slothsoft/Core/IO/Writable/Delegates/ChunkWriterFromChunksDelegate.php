<?php
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;


class ChunkWriterFromChunksDelegate implements ChunkWriterInterface
{
    private $delegate;
    private $result;
    
    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }
    
    public function toChunks(): Generator
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof Generator, "ChunkWriterFromChunksDelegate must return Generator!");
        }
        return $this->result;
    }
}

