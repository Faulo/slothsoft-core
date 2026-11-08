<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromDOMWriter implements StringWriterInterface {
    
    private DOMWriterInterface $source;
    
    /**
     * @param DOMWriterInterface $source
     */
    public function __construct(DOMWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return string
     * @throws \RuntimeException
     */
    public function toString(): string {
        $result = $this->source->toDocument()->saveXML();
        if ($result === false) {
            throw new \RuntimeException('Unable to serialize document.');
        }
        return $result;
    }
}

