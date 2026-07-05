<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use Closure;
use DOMDocument;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;

final class DOMWriterFromDocumentDelegate implements DOMWriterInterface {
    use DOMWriterElementFromDocumentTrait;
    
    private Closure $delegate;
    
    private ?DOMDocument $result = null;
    
    /**
     * @param callable $delegate
     */
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    /**
     * @return DOMDocument
     */
    public function toDocument(): DOMDocument {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof DOMDocument, "DOMWriterFromDocumentDelegate must return DOMDocument!");
        }
        return $this->result;
    }
}
