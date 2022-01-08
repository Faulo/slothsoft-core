<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

class ChunkWriterFromChunkWriterDelegate implements ChunkWriterInterface {

    /** @var callable */
    private $delegate;

    /** @var ChunkWriterInterface */
    private $result;

    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }

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

