<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\AdapterTraits;

use SplFileInfo;
use Slothsoft\Core\IO\FileInfoFactory;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait FileWriterFromDOMTrait {

    public function toFile(): SplFileInfo
    {
        return FileInfoFactory::createFromDocument($this->toDocument());
    }

    public function toString(): string
    {
        return $this->toDocument()->saveXML();
    }
}

