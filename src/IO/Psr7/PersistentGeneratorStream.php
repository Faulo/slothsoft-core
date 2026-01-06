<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use BadMethodCallException;
use Generator;

final class PersistentGeneratorStream implements StreamInterface {
    
    private const NEW = 1;
    
    private const START = 2;
    
    private const MIDDLE = 3;
    
    private const END = 4;
    
    private const ABORTED = 8;
    
    private ?ChunkWriterInterface $writer;
    
    private ?Generator $generator;
    
    private int $state;
    
    public function __construct(ChunkWriterInterface $writer) {
        $this->writer = $writer;
        $this->generator = null;
        $this->state = self::NEW;
    }
    
    private function init(): void {
        if ($this->state === self::ABORTED) {
            throw new BadMethodCallException('The PersistentGeneratorStream was closed before it could finish reading its data.');
        }
        
        if ($this->state === self::NEW) {
            $this->generator = $this->writer->toChunks();
            $this->state = $this->generator->valid() ? self::START : self::END;
        }
    }
    
    public function eof() {
        $this->init();
        
        return $this->state === self::END and $this->bufferIndex >= $this->bufferSize;
    }
    
    public function rewind() {
        $this->seek(0);
    }
    
    public function close() {
        $this->writer = null;
        $this->generator = null;
        if ($this->state !== self::END) {
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
        
        if ($this->state === self::END) {
            $index = $this->bufferIndex;
            $this->bufferIndex = $this->bufferSize;
            return $index === 0 ? $this->buffer : substr($this->buffer, $index);
        }
        
        return $this->read(PHP_INT_MAX);
    }
    
    public function __toString() {
        $this->init();
        
        if ($this->state === self::END) {
            return $this->buffer;
        }
        
        $this->bufferIndex = 0;
        
        return $this->read(PHP_INT_MAX);
    }
    
    public function getSize() {
        $this->init();
        
        if ($this->state !== self::END) {
            $this->read(PHP_INT_MAX);
        }
        
        return $this->bufferSize;
    }
    
    public function tell() {
        return $this->bufferIndex;
    }
    
    public function isReadable() {
        return true;
    }
    
    private string $buffer = '';
    
    private int $bufferIndex = 0;
    
    private int $bufferSize = 0;
    
    public function read($length) {
        $this->init();
        
        if ($this->state !== self::END) {
            while ($this->bufferSize - $this->bufferIndex < $length) {
                if ($this->state === self::START) {
                    $this->state = self::MIDDLE;
                } else {
                    $this->generator->next();
                }
                
                $this->buffer .= (string) $this->generator->current();
                $this->bufferSize = strlen($this->buffer);
                
                if (! $this->generator->valid()) {
                    $this->state = self::END;
                    $this->close();
                    break;
                }
            }
        }
        
        if ($length > $this->bufferSize) {
            $length = $this->bufferSize;
        }
        
        $result = substr($this->buffer, $this->bufferIndex, $length);
        $this->bufferIndex = min($this->bufferIndex + $length, $this->bufferSize);
        
        return $result;
    }
    
    public function isSeekable() {
        return true;
    }
    
    public function seek($offset, $whence = SEEK_SET) {
        if ($whence === SEEK_SET) {
            while ($this->state === self::MIDDLE) {
                $this->read(PHP_INT_MAX);
            }
            
            $this->bufferIndex = (int) $offset;
        } else {
            throw new BadMethodCallException('Cannot seek a PersistentGeneratorStream.');
        }
    }
    
    public function isWritable() {
        return false;
    }
    
    public function write($string) {
        throw new BadMethodCallException('Cannot write a PersistentGeneratorStream.');
    }
}