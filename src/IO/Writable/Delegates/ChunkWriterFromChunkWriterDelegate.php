<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use Closure;
use Generator;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

final class ChunkWriterFromChunkWriterDelegate implements ChunkWriterInterface {
    
    private Closure $delegate;
    
    private ?ChunkWriterInterface $result = null;
    
    /**
     * @param callable $delegate
     * @return void
     */
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    /**
     * @return Generator
     */
    public function toChunks(): Generator {
        return $this->getWriter()->toChunks();
    }
    
    private function getWriter(): ChunkWriterInterface {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof ChunkWriterInterface, "ChunkWriterFromChunkWriterDelegate must return ChunkWriterInterface!");
        }
        return $this->result;
    }
}
