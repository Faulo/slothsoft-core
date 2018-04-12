<?php
namespace Slothsoft\Core\IO\Readable;

use Traversable;

interface ChunkReaderInterface
{
    public function fromChunks(Traversable $chunks);
}

