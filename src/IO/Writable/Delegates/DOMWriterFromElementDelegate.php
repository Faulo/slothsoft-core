<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterDocumentFromElementTrait;
use DOMDocument;
use DOMElement;

class DOMWriterFromElementDelegate implements DOMWriterInterface {
    use DOMWriterDocumentFromElementTrait;

    private $delegate;

    private $result;

    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }

    public function toElement(DOMDocument $targetDoc): DOMElement {
        if ($this->result === null) {
            $this->result = ($this->delegate)($targetDoc);
            assert($this->result instanceof DOMElement, "DOMWriterFromElementDelegate must return DOMElement!");
        }
        return $this->result;
    }
}

