<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

class ChunkWriterFromGenerator implements ChunkWriterInterface {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    public function toChunks(): Generator {
        return $this->generator;
    }
}

