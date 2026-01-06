<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromDOMWriter implements StringWriterInterface {
    
    private DOMWriterInterface $source;
    
    public function __construct(DOMWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toString(): string {
        return $this->source->toDocument()->saveXML();
    }
}

