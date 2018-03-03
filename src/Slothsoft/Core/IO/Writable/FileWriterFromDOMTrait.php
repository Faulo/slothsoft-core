<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\IO\HTTPFile;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait FileWriterFromDOMTrait {

    public function toFile(): HTTPFile
    {
        return HTTPFile::createFromDocument($this->toDocument());
    }

    public function toString(): string
    {
        return $this->toDocument()->saveXML();
    }
}

