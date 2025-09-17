<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

class StreamWriterFromStringWriter implements StreamWriterInterface {
    
    /** @var StringWriterInterface */
    private $source;
    
    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toStream(): StreamInterface {
        $handle = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        fwrite($handle, $this->source->toString());
        rewind($handle);
        return new Stream($handle);
    }
}

