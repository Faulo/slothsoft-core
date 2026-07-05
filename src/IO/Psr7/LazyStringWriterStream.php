<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use OutOfBoundsException;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

final class LazyStringWriterStream implements StreamInterface {
    
    private const NEW = 1;
    
    private const LOADED = 2;
    
    private const ABORTED = 4;
    
    private ?StringWriterInterface $writer;
    
    private int $state;
    
    private string $buffer = '';
    
    private int $bufferIndex = 0;
    
    private int $bufferSize = 0;
    
    /**
     * @param StringWriterInterface $writer
     */
    public function __construct(StringWriterInterface $writer) {
        $this->writer = $writer;
        $this->state = self::NEW;
    }
    
    private function init(): void {
        if ($this->state === self::ABORTED) {
            throw new BadMethodCallException('The LazyStringWriterStream was closed before it could finish reading its data.');
        }
        
        if ($this->state === self::NEW) {
            $this->buffer = $this->writer->toString();
            $this->bufferSize = strlen($this->buffer);
            $this->state = self::LOADED;
        }
    }
    
    /**
     * @return string
     */
    public function __toString(): string {
        $this->rewind();
        return $this->getContents();
    }
    
    /**
     * @return void
     */
    public function close(): void {
        $this->writer = null;
        
        if ($this->state !== self::LOADED) {
            $this->state = self::ABORTED;
        }
    }
    
    /**
     * @return null
     */
    public function detach() {
        $this->close();
        return null;
    }
    
    /**
     * @param ?string $key
     * @return ?array
     */
    public function getMetadata(?string $key = null): ?array {
        return $key === null ? [] : null;
    }
    
    /**
     * @return string
     */
    public function getContents(): string {
        $this->init();
        
        if ($this->bufferIndex >= $this->bufferSize) {
            return '';
        }
        
        $index = $this->bufferIndex;
        $this->bufferIndex = $this->bufferSize;
        return $index === 0 ? $this->buffer : substr($this->buffer, $index);
    }
    
    /**
     * @return int
     */
    public function getSize(): int {
        $this->init();
        return $this->bufferSize;
    }
    
    /**
     * @return int
     */
    public function tell(): int {
        return $this->bufferIndex;
    }
    
    /**
     * @return bool
     */
    public function eof(): bool {
        $this->init();
        return $this->bufferIndex >= $this->bufferSize;
    }
    
    /**
     * @return bool
     */
    public function isSeekable(): bool {
        return true;
    }
    
    /**
     * @param int $offset
     * @param int $whence
     * @return void
     * @throws BadMethodCallException
     * @throws OutOfBoundsException
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {
        $this->init();
        
        switch ($whence) {
            case SEEK_SET:
                $position = $offset;
                break;
            case SEEK_CUR:
                $position = $this->bufferIndex + $offset;
                break;
            case SEEK_END:
                $position = $this->bufferSize + $offset;
                break;
            default:
                throw new BadMethodCallException('Invalid whence for LazyStringWriterStream.');
        }
        
        if ($position < 0 or $position > $this->bufferSize) {
            throw new OutOfBoundsException("Cannot seek to position $position.");
        }
        
        $this->bufferIndex = $position;
    }
    
    /**
     * @return void
     */
    public function rewind(): void {
        $this->seek(0);
    }
    
    /**
     * @return bool
     */
    public function isWritable(): bool {
        return false;
    }
    
    /**
     * @param string $string
     * @return int
     * @throws BadMethodCallException
     */
    public function write(string $string): int {
        throw new BadMethodCallException('Cannot write a LazyStringWriterStream.');
    }
    
    /**
     * @return bool
     */
    public function isReadable(): bool {
        return true;
    }
    
    /**
     * @param int $length
     * @return string
     */
    public function read(int $length): string {
        $this->init();
        
        if ($length <= 0 or $this->bufferIndex >= $this->bufferSize) {
            return '';
        }
        
        if ($this->bufferIndex === 0 and $length >= $this->bufferSize) {
            $this->bufferIndex = $this->bufferSize;
            return $this->buffer;
        }
        
        $length = min($length, $this->bufferSize - $this->bufferIndex);
        
        $result = substr($this->buffer, $this->bufferIndex, $length);
        $this->bufferIndex += $length;
        
        return $result;
    }
}
