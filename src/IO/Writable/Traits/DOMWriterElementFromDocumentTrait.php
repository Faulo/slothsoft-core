<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Traits;

use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait DOMWriterElementFromDocumentTrait {

    public function toElement(DOMDocument $targetDoc): DOMElement {
        return $targetDoc->importNode($this->toDocument()->documentElement, true);
    }
}

