<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use Closure;
use DOMDocument;
use DOMElement;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;

final class DOMWriterFromDOMWriterDelegate implements DOMWriterInterface {
    
    private Closure $delegate;
    
    private ?DOMWriterInterface $result = null;
    
    /**
     * @param callable $delegate
     * @return void
     */
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    /**
     * @return DOMDocument
     */
    public function toDocument(): DOMDocument {
        return $this->getWriter()->toDocument();
    }
    
    /**
     * @param DOMDocument $targetDoc
     * @return DOMElement
     */
    public function toElement(DOMDocument $targetDoc): DOMElement {
        return $this->getWriter()->toElement($targetDoc);
    }
    
    private function getWriter(): DOMWriterInterface {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof DOMWriterInterface, "DOMWriterFromDOMWriterDelegate must return DOMWriterInterface!");
        }
        return $this->result;
    }
}
