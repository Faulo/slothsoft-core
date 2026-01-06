<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Psr7\LazyFileWriterStream;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

final class StreamWriterFromFileWriter implements StreamWriterInterface {
    
    private FileWriterInterface $source;
    
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toStream(): StreamInterface {
        return new LazyFileWriterStream($this->source);
    }
}

