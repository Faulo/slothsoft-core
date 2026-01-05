<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use BadMethodCallException;
use Generator;

final class OneTimeGeneratorStream implements StreamInterface {
    
    private const NEW = 1;
    
    private const START = 2;
    
    private const MIDDLE = 3;
    
    private const END = 4;
    
    private ?ChunkWriterInterface $writer;
    
    private ?Generator $generator;
    
    private int $state;
    
    public function __construct(ChunkWriterInterface $writer) {
        $this->writer = $writer;
        $this->generator = null;
        $this->state = self::NEW;
    }
    
    private function init(): void {
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
        $this->state = self::END;
    }
    
    public function detach() {
        $this->close();
    }
    
    public function getMetadata($key = null) {
        return $key === null ? [] : null;
    }
    
    public function getContents() {
        throw new BadMethodCallException('Cannot getContents a OneTimeGeneratorStream.');
    }
    
    public function __toString() {
        return 'OneTimeGeneratorStream';
    }
    
    public function getSize() {
        return null;
    }
    
    public function tell() {
        throw new BadMethodCallException('Cannot tell a OneTimeGeneratorStream.');
    }
    
    public function isReadable() {
        return true;
    }
    
    private string $buffer = '';
    
    private int $bufferIndex = 0;
    
    private int $bufferSize = 0;
    
    public function read($length) {
        $this->init();
        
        if ($this->eof()) {
            throw new BadMethodCallException('Reached eof of OneTimeGeneratorStream.');
        }
        
        if ($this->bufferIndex === $this->bufferSize) {
            // read next chunk
            
            if ($this->state === self::START) {
                $this->state = self::MIDDLE;
            } else {
                $this->generator->next();
            }
            
            $this->buffer = (string) $this->generator->current();
            $this->bufferIndex = 0;
            $this->bufferSize = strlen($this->buffer);
            
            if (! $this->generator->valid()) {
                $this->state = self::END;
                $this->close();
            }
        }
        
        $read = min($this->bufferSize - $this->bufferIndex, $length);
        $result = substr($this->buffer, $this->bufferIndex, $read);
        $this->bufferIndex += $read;
        return $result;
    }
    
    public function isSeekable() {
        return false;
    }
    
    public function seek($offset, $whence = SEEK_SET) {
        throw new BadMethodCallException('Cannot seek a OneTimeGeneratorStream.');
    }
    
    public function isWritable() {
        return false;
    }
    
    public function write($string) {
        throw new BadMethodCallException('Cannot write a OneTimeGeneratorStream.');
    }
}