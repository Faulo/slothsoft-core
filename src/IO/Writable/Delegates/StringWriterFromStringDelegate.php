<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use Closure;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class StringWriterFromStringDelegate implements StringWriterInterface {
    
    private Closure $delegate;
    
    private ?string $result = null;
    
    /**
     * @param callable $delegate
     */
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    /**
     * @return string
     */
    public function toString(): string {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert(is_string($this->result), "StringWriterFromStringDelegate must return string!");
        }
        return $this->result;
    }
}
