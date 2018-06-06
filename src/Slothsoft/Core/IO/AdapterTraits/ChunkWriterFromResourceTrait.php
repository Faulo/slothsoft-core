<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\IO\Memory;
use Traversable;

trait ChunkWriterFromResourceTrait {

    public function toChunks(): Traversable
    {
        $resource = $this->toResource();
        $chunkSize = 32 * Memory::ONE_KILOBYTE;
        fseek($resource, 0);
        while (! feof($resource)) {
            yield fread($resource, $chunkSize);
        }
    }
}

