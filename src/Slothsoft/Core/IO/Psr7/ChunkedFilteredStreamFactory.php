<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

class ChunkedFilteredStreamFactory implements FilteredStreamWriterInterface {

    public function toFilteredStream(StreamInterface $stream): StreamInterface {
        return new ChunkedFilteredStream($stream);
    }
}

