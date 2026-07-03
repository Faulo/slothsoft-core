<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Traits;

use DOMDocument;
use DOMElement;
use RuntimeException;

/**
 *
 * @author Daniel Schulz
 *
 */
trait DOMWriterElementFromDocumentTrait {
    
    public function toElement(DOMDocument $targetDoc): DOMElement {
        $element = $targetDoc->importNode($this->toDocument()->documentElement, true);
        
        if (! $element instanceof DOMElement) {
            throw new RuntimeException('Unable to import document element.');
        }
        
        return $element;
    }
}
