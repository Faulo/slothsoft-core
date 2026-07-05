<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Slothsoft\Core\IO\Memory;

abstract class AbstractFilteredStream implements StreamInterface {
    use StreamDecoratorTrait;
    
    const STATE_OPENING = 1;
    
    const STATE_PROCESSING = 2;
    
    const STATE_CLOSING = 3;
    
    const STATE_CLOSED = 4;
    
    private $stream;
    
    private int $state;
    
    /**
     * @param StreamInterface $stream
     * @return void
     */
    public function __construct(StreamInterface $stream) {
        $this->stream = new CachingStream($stream);
        $this->state = static::STATE_OPENING;
    }
    
    /**
     * @param mixed $length
     * @return mixed
     * @throws RuntimeException
     */
    public function read($length) {
        switch ($this->state) {
            case static::STATE_OPENING:
                $this->state = static::STATE_PROCESSING;
                return $this->processHeader();
            case static::STATE_PROCESSING:
                $data = $this->processPayload($this->stream->read($length));
                if ($this->stream->eof()) {
                    $this->state = static::STATE_CLOSING;
                }
                return $data;
            case static::STATE_CLOSING:
                $this->state = static::STATE_CLOSED;
                return $this->processFooter();
            case static::STATE_CLOSED:
                throw new RuntimeException('The stream has been closed.');
        }
        
        throw new RuntimeException("Invalid stream state '$this->state'.");
    }
    
    /**
     * @return mixed
     */
    public function getContents() {
        $buffer = '';
        while (! $this->eof()) {
            $buffer .= $this->read(Memory::ONE_KILOBYTE);
        }
        return $buffer;
    }
    
    /**
     * @return mixed
     */
    public function eof() {
        return $this->state === static::STATE_CLOSED;
    }
    
    /**
     * @return mixed
     */
    public function isSeekable() {
        return $this->stream->isSeekable();
    }
    
    /**
     * @return mixed
     */
    public function getSize() {
        if ($this->isSeekable()) {
            $ret = strlen($this->getContents());
            $this->rewind();
            return $ret;
        } else {
            return null;
        }
    }
    
    /**
     * @param mixed $offset
     * @param mixed $whence
     * @return void
     * @throws BadMethodCallException
     */
    public function seek($offset, $whence = SEEK_SET) {
        if ($offset === 0 and $whence === SEEK_SET) {
            $this->stream->rewind();
            $this->state = static::STATE_OPENING;
        } else {
            throw new BadMethodCallException('FilteredStreams only support full rewind.');
        }
    }
    
    /**
     * @return string
     */
    abstract protected function processHeader(): string;
    
    /**
     * @param string $data
     * @return string
     */
    abstract protected function processPayload(string $data): string;
    
    /**
     * @return string
     */
    abstract protected function processFooter(): string;
}
