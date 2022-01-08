<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

class ChunkWriterFromChunksDelegate implements ChunkWriterInterface {

    /** @var callable */
    private $delegate;

    /** @var Generator */
    private $result;

    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }

    public function toChunks(): Generator {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof Generator, "ChunkWriterFromChunksDelegate must return Generator!");
        }
        return $this->result;
    }
}

