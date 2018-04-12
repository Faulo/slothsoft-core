<?php
namespace Slothsoft\Core\IO\Writable;

trait ResourceWriterFromChunksTrait {
    public function toResource() {
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, $this->toDocument()->saveXML());
        fseek($resource, 0);
        return $resource;
    }
}

