<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use DOMDocument;

final class DOMWriterFromStringWriter implements DOMWriterInterface {
    use DOMWriterElementFromDocumentTrait;
    
    private StringWriterInterface $source;
    
    private ?string $documentURI;
    
    private bool $isHtml;
    
    public function __construct(StringWriterInterface $source, ?string $documentURI = null, bool $isHtml = false) {
        $this->source = $source;
        $this->documentURI = $documentURI;
        $this->isHtml = $isHtml;
    }
    
    private ?DOMDocument $document = null;
    
    public function toDocument(): DOMDocument {
        if ($this->document === null) {
            $this->document = DOMHelper::parseDocument($this->source->toString(), $this->isHtml);
            
            if ($this->documentURI !== null) {
                $this->document->documentURI = $this->documentURI;
            }
        }
        
        return $this->document;
    }
}

