<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use Generator;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

final class OneTimeGeneratorStream implements StreamInterface {
    
    private const NEW = 1;
    
    private const START = 2;
    
    private const MIDDLE = 3;
    
    private const END = 4;
    
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
        $this->state = self::END;
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
     * @throws BadMethodCallException
     */
    public function getContents(): string {
        throw new BadMethodCallException('Cannot getContents a OneTimeGeneratorStream.');
    }
    
    /**
     * @return string
     */
    public function __toString(): string {
        return 'OneTimeGeneratorStream';
    }
    
    /**
     * @return null
     */
    public function getSize() {
        return null;
    }
    
    /**
     * @return int
     * @throws BadMethodCallException
     */
    public function tell(): int {
        throw new BadMethodCallException('Cannot tell a OneTimeGeneratorStream.');
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
     * @throws BadMethodCallException
     */
    public function read(int $length): string {
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
    
    /**
     * @return bool
     */
    public function isSeekable(): bool {
        return false;
    }
    
    /**
     * @param int $offset
     * @param int $whence
     * @return void
     * @throws BadMethodCallException
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {
        throw new BadMethodCallException('Cannot seek a OneTimeGeneratorStream.');
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
        throw new BadMethodCallException('Cannot write a OneTimeGeneratorStream.');
    }
}
