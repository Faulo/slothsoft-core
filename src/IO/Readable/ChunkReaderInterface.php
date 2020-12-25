<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use Generator;

interface ChunkReaderInterface {

    public function fromChunks(Generator $chunks);
}

