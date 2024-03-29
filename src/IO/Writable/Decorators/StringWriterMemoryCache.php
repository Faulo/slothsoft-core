<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\StringWriterInterface;

class StringWriterMemoryCache implements StringWriterInterface {

    /** @var StringWriterInterface */
    private $source;

    /** @var string */
    private $result;

    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }

    public function toString(): string {
        if ($this->result === null) {
            $this->result = $this->source->toString();
        }
        return $this->result;
    }
}

