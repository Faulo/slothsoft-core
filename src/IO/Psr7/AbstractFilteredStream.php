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
     */
    public function __construct(StreamInterface $stream) {
        $this->stream = new CachingStream($stream);
        $this->state = static::STATE_OPENING;
    }
    
    /**
     * @return string
     */
    public function __toString(): string {
        return $this->getContents();
    }
    
    /**
     * @return void
     */
    public function close(): void {
        $this->stream->close();
    }
    
    /**
     * @return resource|null
     */
    public function detach() {
        return $this->stream->detach();
    }
    
    /**
     * @param int $length
     * @return string
     * @throws RuntimeException
     */
    public function read(int $length): string {
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
     * @return string
     */
    public function getContents(): string {
        $buffer = '';
        while (! $this->eof()) {
            $buffer .= $this->read(Memory::ONE_KILOBYTE);
        }
        return $buffer;
    }
    
    /**
     * @return bool
     */
    public function eof(): bool {
        return $this->state === static::STATE_CLOSED;
    }
    
    /**
     * @return bool
     */
    public function isSeekable(): bool {
        return $this->stream->isSeekable();
    }
    
    /**
     * @return int|null
     */
    public function getSize(): ?int {
        if ($this->isSeekable()) {
            $ret = strlen($this->getContents());
            $this->rewind();
            return $ret;
        } else {
            return null;
        }
    }
    
    /**
     * @return int
     */
    public function tell(): int {
        return $this->stream->tell();
    }
    
    /**
     * @param int $offset
     * @param int $whence
     * @return void
     * @throws BadMethodCallException
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {
        if ($offset === 0 and $whence === SEEK_SET) {
            $this->stream->rewind();
            $this->state = static::STATE_OPENING;
        } else {
            throw new BadMethodCallException('FilteredStreams only support full rewind.');
        }
    }
    
    /**
     * @return bool
     */
    public function isWritable(): bool {
        return $this->stream->isWritable();
    }
    
    /**
     * @param string $string
     * @return int
     */
    public function write(string $string): int {
        return $this->stream->write($string);
    }
    
    /**
     * @return bool
     */
    public function isReadable(): bool {
        return $this->stream->isReadable();
    }
    
    /**
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getMetadata(?string $key = null) {
        return $this->stream->getMetadata($key);
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
