<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Traits;

use DOMDocument;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait DOMWriterDocumentFromElementTrait {

    public function toDocument(): DOMDocument {
        $targetDoc = new DOMDocument();
        $targetDoc->appendChild($this->toElement($targetDoc));
        return $targetDoc;
    }
}

