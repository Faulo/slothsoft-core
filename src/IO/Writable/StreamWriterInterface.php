<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Psr\Http\Message\StreamInterface;

interface StreamWriterInterface {

    public function toStream(): StreamInterface;
}

