<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Traits;

use DOMDocument;

/**
 * Implements document output for DOM writers that already provide element output.
 *
 * @author Daniel Schulz
 * @since 2018-03-03
 */
trait DOMWriterDocumentFromElementTrait {
    
    public function toDocument(): DOMDocument {
        $targetDoc = new DOMDocument();
        $targetDoc->appendChild($this->toElement($targetDoc));
        return $targetDoc;
    }
}
