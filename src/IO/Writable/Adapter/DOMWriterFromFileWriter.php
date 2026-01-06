<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use DOMDocument;

final class DOMWriterFromFileWriter implements DOMWriterInterface {
    use DOMWriterElementFromDocumentTrait;
    
    private FileWriterInterface $source;
    
    private ?string $documentURI;
    
    private bool $isHtml;
    
    public function __construct(FileWriterInterface $source, ?string $documentURI = null, bool $isHtml = false) {
        $this->source = $source;
        $this->documentURI = $documentURI;
        $this->isHtml = $isHtml;
    }
    
    private ?DOMDocument $document = null;
    
    public function toDocument(): DOMDocument {
        if ($this->document === null) {
            $this->document = DOMHelper::loadDocument((string) $this->source->toFile(), $this->isHtml);
            
            if ($this->documentURI !== null) {
                $this->document->documentURI = $this->documentURI;
            }
        }
        
        return $this->document;
    }
}

