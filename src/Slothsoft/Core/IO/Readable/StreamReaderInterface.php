<?php
namespace Slothsoft\Core\IO\Readable;;

use Psr\Http\Message\StreamInterface;

interface StreamReaderInterface
{
    public function fromStream(StreamInterface $stream);
}

