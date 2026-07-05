<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use DOMDocument;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;

final class DOMWriterFromStringWriter implements DOMWriterInterface {
    use DOMWriterElementFromDocumentTrait;
    
    private StringWriterInterface $source;
    
    private ?string $documentURI;
    
    private bool $isHtml;
    
    /**
     * @param StringWriterInterface $source
     * @param string|null $documentURI
     * @param bool $isHtml
     */
    public function __construct(StringWriterInterface $source, ?string $documentURI = null, bool $isHtml = false) {
        $this->source = $source;
        $this->documentURI = $documentURI;
        $this->isHtml = $isHtml;
    }
    
    /**
     * @return DOMDocument
     */
    public function toDocument(): DOMDocument {
        $document = DOMHelper::parseDocument($this->source->toString(), $this->isHtml);
        
        if ($this->documentURI !== null) {
            $document->documentURI = $this->documentURI;
        }
        
        return $document;
    }
}

