<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

trait ResourceWriterFromDOMTrait {
    public function toResource() {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        fwrite($resource, $this->toDocument()->saveXML());
        fseek($resource, 0);
        return $resource;
    }
}

