<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait FileWriterStringFromFileTrait {

    public function toString(): string
    {
        return $this->toFile()->getContents();
    }
}

