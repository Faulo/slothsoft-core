<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterMemoryCache implements StringWriterInterface {
    
    private StringWriterInterface $source;
    
    private ?string $result = null;
    
    /**
     * @param StringWriterInterface $source
     * @return void
     */
    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return string
     */
    public function toString(): string {
        if ($this->result === null) {
            $this->result = $this->source->toString();
        }
        return $this->result;
    }
}
