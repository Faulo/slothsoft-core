<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\AdapterTraits;

use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait DOMWriterFromStringTrait {
    
    public function toDocument(): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadXML($this->toString(), LIBXML_PARSEHUGE);
        return $document;
    }
    
    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        $fragment = $targetDoc->createDocumentFragment();
        $fragment->appendXML($this->toString());
        return $fragment->removeChild($fragment->firstChild);
    }
}

