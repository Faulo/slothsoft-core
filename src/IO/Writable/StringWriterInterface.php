<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

interface StringWriterInterface {
    
    /**
     * Converts the object's data to a string.
     *
     * @return string
     */
    public function toString(): string;
}

