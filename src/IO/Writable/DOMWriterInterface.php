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
interface DOMWriterInterface {
    
    /**
     * Converts the object's data to an element for an existing document.
     * Subsequent calls are expected to return a new element each time.
     *
     * @param DOMDocument $targetDoc
     * @return DOMElement
     */
    public function toElement(DOMDocument $targetDoc): DOMElement;
    
    /**
     * Converts the object's data to a document.
     * Subsequent calls are expected to return the same document each time.
     *
     * @return DOMDocument
     */
    public function toDocument(): DOMDocument;
}

