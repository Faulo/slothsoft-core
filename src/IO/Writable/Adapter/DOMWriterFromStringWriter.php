<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use DOMDocument;
use DOMElement;

class DOMWriterFromStringWriter implements DOMWriterInterface {
    
    private StringWriterInterface $source;
    
    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }
    
    private ?DOMDocument $document = null;
    
    public function toDocument(): DOMDocument {
        if ($this->document === null) {
            $this->document = new DOMDocument();
            $this->document->loadXML($this->source->toString());
        }
        
        return $this->document;
    }
    
    public function toElement(DOMDocument $targetDoc): DOMElement {
        $fragment = $targetDoc->createDocumentFragment();
        $fragment->appendXML($this->source->toString());
        return $fragment->removeChild($fragment->firstChild);
    }
}

