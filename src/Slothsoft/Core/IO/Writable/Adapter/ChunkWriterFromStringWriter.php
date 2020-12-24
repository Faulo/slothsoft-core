<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use Generator;

class ChunkWriterFromStringWriter implements ChunkWriterInterface {

    private $source;

    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }

    public function toChunks(): Generator {
        yield $this->source->toString();
    }
}

