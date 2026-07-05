<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use Generator;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

final class ChunkWriterMemoryCache implements ChunkWriterInterface {
    
    private ChunkWriterInterface $source;
    
    private ?array $chunks = null;
    
    /**
     * @param ChunkWriterInterface $source
     * @return void
     */
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return Generator
     */
    public function toChunks(): Generator {
        if ($this->chunks === null) {
            $this->chunks = [];
            foreach ($this->source->toChunks() as $chunk) {
                $this->chunks[] = $chunk;
                yield $chunk;
            }
        } else {
            yield from $this->chunks;
        }
    }
}

