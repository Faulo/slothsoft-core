<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Generator;

interface ChunkWriterInterface 
{
    public function toChunks(): Generator;
}

