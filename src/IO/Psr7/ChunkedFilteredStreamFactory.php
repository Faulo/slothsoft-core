<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

final class ChunkedFilteredStreamFactory implements FilteredStreamWriterInterface {
    
    /**
     * @param StreamInterface $stream
     * @return StreamInterface
     */
    public function toFilteredStream(StreamInterface $stream): StreamInterface {
        return new ChunkedFilteredStream($stream);
    }
}
