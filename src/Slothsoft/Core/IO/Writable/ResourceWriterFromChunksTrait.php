<?php
namespace Slothsoft\Core\IO\Writable;

trait ResourceWriterFromChunksTrait {
    public function toResource() {
        $resource = fopen('php://temp', 'w+');
        foreach ($this->toChunks() as $chunk) {
            fwrite($resource);
        }
        return $resource;
    }
}

