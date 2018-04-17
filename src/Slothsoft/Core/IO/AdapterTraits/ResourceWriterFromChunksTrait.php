<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

trait ResourceWriterFromChunksTrait {
    public function toResource() {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        foreach ($this->toChunks() as $chunk) {
            fwrite($resource);
        }
        return $resource;
    }
}

