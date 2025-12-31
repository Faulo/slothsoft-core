<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

final class ChunkWriterMemoryCache implements ChunkWriterInterface {
    
    private ChunkWriterInterface $source;
    
    private ?array $chunks = null;
    
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
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

