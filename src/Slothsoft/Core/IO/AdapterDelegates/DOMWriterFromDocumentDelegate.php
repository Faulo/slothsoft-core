<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\AdapterDelegates;

use Slothsoft\Core\IO\Writable\DOMWriterElementFromDocumentTrait;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use DOMDocument;

class DOMWriterFromDocumentDelegate implements DOMWriterInterface
{
    use DOMWriterElementFromDocumentTrait;

    private $delegate;
    private $result;

    public function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    public function toDocument(): DOMDocument
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof DOMDocument, "DOMWriterFromDocumentDelegate must return DOMDocument!");
        }
        return $this->result;
    }
}

