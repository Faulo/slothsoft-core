<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Generator;

final class ChunkWriterFromFileWriter implements ChunkWriterInterface {
    
    private FileWriterInterface $source;
    
    private int $chunkSize;
    
    public function __construct(FileWriterInterface $source, int $chunkSize = Memory::ONE_KILOBYTE) {
        $this->source = $source;
        $this->chunkSize = $chunkSize;
    }
    
    public function toChunks(): Generator {
        $handle = $this->source->toFile()->openFile(StreamWrapperInterface::MODE_OPEN_READONLY);
        while (! $handle->eof()) {
            yield $handle->fread($this->chunkSize);
        }
        unset($handle);
    }
}

