<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Mergers;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

class ChunkWriterMerger implements ChunkWriterInterface {

    private $writers;

    public function __construct(ChunkWriterInterface ...$writers) {
        $this->writers = $writers;
    }

    public function toChunks(): Generator {
        foreach ($this->writers as $writer) {
            yield from $writer->toChunks();
        }
    }
}

