<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Traversable;

trait ChunkWriterFromFileTrait {
    public function toChunks(): Traversable
    {
        yield $this->toFile()->getContents();
    }
}

