<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Generator;


class ChunkWriterFromStreamWriter implements ChunkWriterInterface
{
    private $source;
    public function __construct(StreamWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toChunks(): Generator
    {
        $handle = $this->source->toStream();
        while (!$handle->eof()) {
            yield $handle->fread(Memory::ONE_KILOBYTE);
        }
        $handle->close();
    }

}

