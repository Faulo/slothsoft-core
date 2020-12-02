<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

class StreamWriterFromStreamDelegate implements StreamWriterInterface {

    private $delegate;

    private $result;

    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }

    public function toStream(): StreamInterface {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof StreamInterface, "StreamWriterFromStreamDelegate must return StreamInterface!");
        }
        return $this->result;
    }
}

