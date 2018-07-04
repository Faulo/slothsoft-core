<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use DOMDocument;

class DOMWriterMemoryCache implements DOMWriterInterface
{
    use DOMWriterElementFromDocumentTrait;
    
    private $source;
    private $result;
    
    public function __construct(DOMWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toDocument(): DOMDocument
    {
        if ($this->result === null) {
            $this->result = $this->source->toDocument();
        }
        return $this->result;
    }
}

