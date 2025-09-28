<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterDocumentFromElementTrait;
use Closure;
use DOMDocument;
use DOMElement;

class DOMWriterFromElementDelegate implements DOMWriterInterface {
    use DOMWriterDocumentFromElementTrait;
    
    private Closure $delegate;
    
    private ?DOMElement $result = null;
    
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    public function toElement(DOMDocument $targetDoc): DOMElement {
        if ($this->result === null) {
            $this->result = ($this->delegate)($targetDoc);
            assert($this->result instanceof DOMElement, "DOMWriterFromElementDelegate must return DOMElement!");
        }
        return $this->result->ownerDocument === $targetDoc ? $this->result : $targetDoc->importNode($this->result, true);
    }
}

