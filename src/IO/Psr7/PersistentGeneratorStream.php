<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use Generator;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

final class PersistentGeneratorStream implements StreamInterface {
    
    private const NEW = 1;
    
    private const START = 2;
    
    private const MIDDLE = 3;
    
    private const END = 4;
    
    private const ABORTED = 8;
    
    private ?ChunkWriterInterface $writer;
    
    private ?Generator $generator;
    
    private int $state;
    
    /**
     * @param ChunkWriterInterface $writer
     */
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
    
    /**
     * @return bool
     */
    public function eof(): bool {
        $this->init();
        
        return $this->state === self::END and $this->bufferIndex >= $this->bufferSize;
    }
    
    /**
     * @return void
     */
    public function rewind(): void {
        $this->seek(0);
    }
    
    /**
     * @return void
     */
    public function close(): void {
        $this->writer = null;
        $this->generator = null;
        if ($this->state !== self::END) {
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
     * @param string|null $key
     * @return array|null
     */
    public function getMetadata(?string $key = null): ?array {
        return $key === null ? [] : null;
    }
    
    /**
     * @return string
     */
    public function getContents(): string {
        $this->init();
        
        if ($this->state === self::END) {
            $index = $this->bufferIndex;
            $this->bufferIndex = $this->bufferSize;
            return $index === 0 ? $this->buffer : substr($this->buffer, $index);
        }
        
        return $this->read(PHP_INT_MAX);
    }
    
    /**
     * @return string
     */
    public function __toString(): string {
        $this->init();
        
        if ($this->state === self::END) {
            return $this->buffer;
        }
        
        $this->bufferIndex = 0;
        
        return $this->read(PHP_INT_MAX);
    }
    
    /**
     * @return int
     */
    public function getSize(): int {
        $this->init();
        
        if ($this->state !== self::END) {
            $index = $this->bufferIndex;
            $this->read(PHP_INT_MAX);
            $this->bufferIndex = $index;
        }
        
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
    public function isReadable(): bool {
        return true;
    }
    
    private string $buffer = '';
    
    private int $bufferIndex = 0;
    
    private int $bufferSize = 0;
    
    /**
     * @param int $length
     * @return string
     */
    public function read(int $length): string {
        $this->init();
        
        if ($this->state !== self::END) {
            while ($this->bufferSize - $this->bufferIndex < $length) {
                if ($this->state === self::START) {
                    $this->state = self::MIDDLE;
                } else {
                    $this->generator->next();
                }
                
                $this->buffer .= $this->generator->current();
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
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {
        if ($whence === SEEK_SET) {
            while ($this->state === self::MIDDLE) {
                $this->read(PHP_INT_MAX);
            }
            
            $this->bufferIndex = $offset;
        } else {
            throw new BadMethodCallException('Cannot seek a PersistentGeneratorStream.');
        }
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
        throw new BadMethodCallException('Cannot write a PersistentGeneratorStream.');
    }
}
