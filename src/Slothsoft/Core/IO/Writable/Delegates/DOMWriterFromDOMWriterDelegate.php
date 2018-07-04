<?php
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use DOMDocument;
use DOMElement;

class DOMWriterFromDOMWriterDelegate implements DOMWriterInterface
{
    private $delegate;
    private $result;
    
    public function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }
    
    public function toDocument(): DOMDocument
    {
        return $this->getWriter()->toDocument();
    }
    
    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        return $this->getWriter()->toElement($targetDoc);
    }
    
    private function getWriter(): DOMWriterInterface
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof DOMWriterInterface, "DOMWriterFromDOMWriterDelegate must return DOMWriterInterface!");
        }
        return $this->result;
    }
    
}

