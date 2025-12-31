<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Psr\Http\Message\StreamInterface;

interface StreamWriterInterface {
    
    /**
     * Converts the object's data to a stream.
     * Subsequent calls are expected to return a new stream each time.
     *
     * @return StreamInterface
     */
    public function toStream(): StreamInterface;
}

