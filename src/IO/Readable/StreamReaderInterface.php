<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use Psr\Http\Message\StreamInterface;

interface StreamReaderInterface {
    
    public function fromStream(StreamInterface $stream);
}

