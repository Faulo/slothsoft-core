<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Symfony\Component\Process\Process;
use Generator;

class ChunkWriterFromProcess implements ChunkWriterInterface {

    /** @var Process */
    private $process;

    public function __construct(Process $process) {
        $this->process = $process;
    }

    public function toChunks(): Generator {
        $this->process->start();
        foreach ($this->process as $data) {
            yield $data;
        }
    }
}

