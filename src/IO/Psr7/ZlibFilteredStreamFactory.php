<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

final class ZlibFilteredStreamFactory implements FilteredStreamWriterInterface {
    
    private int $zlibCoding;
    
    public function __construct(int $zlibCoding) {
        $this->zlibCoding = $zlibCoding;
    }
    
    public function toFilteredStream(StreamInterface $stream): StreamInterface {
        return new ZlibFilteredStream($stream, $this->zlibCoding);
    }
}
