<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Psr\Http\Message\StreamInterface;

interface FilteredStreamWriterInterface {
    
    /**
     * Appends a filter to a stream.
     *
     * @param StreamInterface $stream
     * @return StreamInterface
     */
    public function toFilteredStream(StreamInterface $stream): StreamInterface;
}

