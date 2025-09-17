<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

class StreamWriterFromFileWriter implements StreamWriterInterface {
    
    /** @var FileWriterInterface */
    private $source;
    
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toStream(): StreamInterface {
        return new LazyOpenStream((string) $this->source->toFile(), StreamWrapperInterface::MODE_OPEN_READONLY);
    }
}

