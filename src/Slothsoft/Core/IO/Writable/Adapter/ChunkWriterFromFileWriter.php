<?php
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Generator;


class ChunkWriterFromFileWriter implements ChunkWriterInterface
{
    private $source;
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toChunks(): Generator
    {
        $handle = $this->source->toFile()->openFile(StreamWrapperInterface::MODE_OPEN_READONLY);
        while (!$handle->eof()) {
            yield $handle->fread(Memory::ONE_KILOBYTE);
        }
        unset($handle);
    }
}

