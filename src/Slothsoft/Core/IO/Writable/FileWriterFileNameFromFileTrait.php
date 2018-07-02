<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait FileWriterFileNameFromFileTrait {

    public function toFileName(): string
    {
        return $this->toFile()->getFilename();
    }
}

