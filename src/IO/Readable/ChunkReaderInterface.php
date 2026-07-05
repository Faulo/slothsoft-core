<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Readable;

use Generator;

interface ChunkReaderInterface {
    
    /**
     * @param Generator $chunks
     * @return void
     */
    public function fromChunks(Generator $chunks);
}

