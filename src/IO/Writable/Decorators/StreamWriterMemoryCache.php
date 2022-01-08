<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\StreamWriterInterface;

class StreamWriterMemoryCache implements StreamWriterInterface {

    /** @var StreamWriterInterface */
    private $source;

    /** @var StreamInterface */
    private $result;

    public function __construct(StreamWriterInterface $source) {
        $this->source = $source;
    }

    public function toStream(): StreamInterface {
        if ($this->result === null or ! $this->result->isReadable()) {
            $this->result = $this->source->toStream();
        }
        return $this->result;
    }
}

