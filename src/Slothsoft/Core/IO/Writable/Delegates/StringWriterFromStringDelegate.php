<?php
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\StringWriterInterface;

class StringWriterFromStringDelegate implements StringWriterInterface
{
    private $delegate;
    private $result;
    
    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }
    
    public function toString(): string
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert(is_string($this->result), "StringWriterFromStringDelegate must return string!");
        }
        return $this->result;
    }
}

