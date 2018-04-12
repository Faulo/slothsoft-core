<?php
namespace Slothsoft\Core\IO\Writable;

use Traversable;

interface ChunkWriterInterface
{
    public function toChunks() : Traversable;
}

