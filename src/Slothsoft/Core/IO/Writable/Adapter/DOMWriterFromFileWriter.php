<?php
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
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toDocument(): DOMDocument
    {
        return DOMHelper::loadDocument((string) $this->source->toFile());
    }
}

