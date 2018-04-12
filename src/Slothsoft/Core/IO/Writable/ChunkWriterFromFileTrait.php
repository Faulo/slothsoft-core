<?php
namespace Slothsoft\Core\IO\Writable;

trait ChunkWriterFromFileTrait {
    public function toChunks(): \Traversable
    {
        yield $this->toFile()->getContents();
    }
}

