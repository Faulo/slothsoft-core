<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use BadMethodCallException;
use OutOfBoundsException;

final class LazyStringWriterStream implements StreamInterface {
    
    private const NEW = 1;
    
    private const LOADED = 2;
    
    private const ABORTED = 4;
    
    private ?StringWriterInterface $writer;
    
    private int $state;
    
    private string $buffer = '';
    
    private int $bufferIndex = 0;
    
    private int $bufferSize = 0;
    
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
    
    public function __toString() {
        $this->rewind();
        return $this->getContents();
    }
    
    public function close() {
        $this->writer = null;
        
        if ($this->state !== self::LOADED) {
            $this->state = self::ABORTED;
        }
    }
    
    public function detach() {
        $this->close();
        return null;
    }
    
    public function getMetadata($key = null) {
        return $key === null ? [] : null;
    }
    
    public function getContents() {
        $this->init();
        
        if ($this->bufferIndex >= $this->bufferSize) {
            return '';
        }
        
        $result = substr($this->buffer, $this->bufferIndex);
        $this->bufferIndex = $this->bufferSize;
        
        return $result;
    }
    
    public function getSize() {
        $this->init();
        return $this->bufferSize;
    }
    
    public function tell() {
        return $this->bufferIndex;
    }
    
    public function eof() {
        $this->init();
        return $this->bufferIndex >= $this->bufferSize;
    }
    
    public function isSeekable() {
        return true;
    }
    
    public function seek($offset, $whence = SEEK_SET) {
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
    
    public function rewind() {
        $this->seek(0, SEEK_SET);
    }
    
    public function isWritable() {
        return false;
    }
    
    public function write($string) {
        throw new BadMethodCallException('Cannot write a LazyStringWriterStream.');
    }
    
    public function isReadable() {
        return true;
    }
    
    public function read($length) {
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
