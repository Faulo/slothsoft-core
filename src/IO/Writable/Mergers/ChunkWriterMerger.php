<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Mergers;

use Generator;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

final class ChunkWriterMerger implements ChunkWriterInterface {
    
    private array $writers;
    
    /**
     * @param ChunkWriterInterface ...$writers
     * @return void
     */
    public function __construct(ChunkWriterInterface ...$writers) {
        $this->writers = $writers;
    }
    
    /**
     * @return Generator
     */
    public function toChunks(): Generator {
        foreach ($this->writers as $writer) {
            yield from $writer->toChunks();
        }
    }
}
