<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use DOMDocument;

class DOMWriterFromFileWriter implements DOMWriterInterface
{
    use DOMWriterElementFromDocumentTrait;
    
    private $source;
    private $documentURI;
    public function __construct(FileWriterInterface $source, ?string $documentURI = null) {
        $this->source = $source;
        $this->documentURI = $documentURI;
    }
    
    public function toDocument(): DOMDocument
    {
        $document = DOMHelper::loadDocument((string) $this->source->toFile());
        if ($this->documentURI !== null) {
            $document->documentURI = $this->documentURI;
        }
        return $document;
    }
}

