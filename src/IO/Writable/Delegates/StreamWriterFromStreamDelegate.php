<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;
use Closure;

class StreamWriterFromStreamDelegate implements StreamWriterInterface {
    
    private Closure $delegate;
    
    private ?StreamInterface $result = null;
    
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    public function toStream(): StreamInterface {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof StreamInterface, "StreamWriterFromStreamDelegate must return StreamInterface!");
        }
        return $this->result;
    }
}

