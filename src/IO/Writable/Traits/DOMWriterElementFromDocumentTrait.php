<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Traits;

use DOMDocument;
use DOMElement;
use RuntimeException;

/**
 * Implements element output for DOM writers that already provide document output.
 *
 * @author Daniel Schulz
 * @since 2018-03-03
 */
trait DOMWriterElementFromDocumentTrait {
    
    /**
     * @param DOMDocument $targetDoc
     * @return DOMElement
     * @throws RuntimeException
     */
    public function toElement(DOMDocument $targetDoc): DOMElement {
        $element = $targetDoc->importNode($this->toDocument()->documentElement, true);
        
        if (! $element instanceof DOMElement) {
            throw new RuntimeException('Unable to import document element.');
        }
        
        return $element;
    }
}
