<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Closure;
use Generator;

final class ChunkWriterFromChunksDelegate implements ChunkWriterInterface {
    
    private Closure $delegate;
    
    private ?Generator $result = null;
    
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    public function toChunks(): Generator {
        if ($this->result === null or ! $this->result->valid()) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof Generator, "ChunkWriterFromChunksDelegate must return Generator!");
        }
        return $this->result;
    }
}

