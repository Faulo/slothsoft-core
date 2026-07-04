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
    
    public function fromDocument(DOMDocument $sourceDoc): void;
    
    public function fromElement(DOMElement $sourceElement): void;
}
