<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

class StringWriterFromDOMWriter implements StringWriterInterface {

    /** @var DOMWriterInterface */
    private $source;

    public function __construct(DOMWriterInterface $source) {
        $this->source = $source;
    }

    public function toString(): string {
        return $this->source->toDocument()->saveXML();
    }
}

