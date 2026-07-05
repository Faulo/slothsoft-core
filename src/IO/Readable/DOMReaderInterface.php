<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Readable;

use DOMDocument;
use DOMElement;

/**
 * Reader that imports state from DOM documents or elements.
 *
 * @author Daniel Schulz
 * @since 2018-03-03
 */
interface DOMReaderInterface {
    
    /**
     * @param DOMDocument $sourceDoc
     * @return void
     */
    public function fromDocument(DOMDocument $sourceDoc): void;
    
    /**
     * @param DOMElement $sourceElement
     * @return void
     */
    public function fromElement(DOMElement $sourceElement): void;
}
