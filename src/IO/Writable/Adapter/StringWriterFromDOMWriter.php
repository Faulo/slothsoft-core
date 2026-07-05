<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromDOMWriter implements StringWriterInterface {
    
    private DOMWriterInterface $source;
    
    /**
     * @param DOMWriterInterface $source
     * @return void
     */
    public function __construct(DOMWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return string
     */
    public function toString(): string {
        return $this->source->toDocument()->saveXML();
    }
}

