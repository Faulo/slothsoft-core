<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait DOMWriterFromFileTrait {

    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        return $targetDoc->importNode($this->toDocument()->documentElement, true);
    }

    public function toDocument(): DOMDocument
    {
        return $this->toFile()->getDocument();
    }
}
