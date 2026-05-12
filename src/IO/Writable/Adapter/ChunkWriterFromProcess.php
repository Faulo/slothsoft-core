<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Generator;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Symfony\Component\Process\Process;

final class ChunkWriterFromProcess implements ChunkWriterInterface {
    
    private Process $process;
    
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

