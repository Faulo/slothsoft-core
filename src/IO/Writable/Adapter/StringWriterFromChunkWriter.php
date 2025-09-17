<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

class StringWriterFromChunkWriter implements StringWriterInterface {
    
    /** @var ChunkWriterInterface */
    private $source;
    
    public function __construct(ChunkWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toString(): string {
        $handle = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        foreach ($this->source->toChunks() as $data) {
            fwrite($handle, $data);
        }
        rewind($handle);
        return stream_get_contents($handle);
    }
}

