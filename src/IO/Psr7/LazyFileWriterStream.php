<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use SplFileInfo;
use SplFileObject;

final class LazyFileWriterStream implements StreamInterface {
    
    private ?FileWriterInterface $writer;
    
    /**
     * @param FileWriterInterface $writer
     */
    public function __construct(FileWriterInterface $writer) {
        $this->writer = $writer;
    }
    
    private function getWriter(): FileWriterInterface {
        if ($this->writer === null) {
            throw new BadMethodCallException('The LazyFileWriterStream was closed before it could load its data.');
        }
        
        return $this->writer;
    }
    
    private ?SplFileInfo $file = null;
    
    private function getFile(): SplFileInfo {
        return $this->file ??= $this->getWriter()->toFile();
    }
    
    private ?SplFileObject $handle = null;
    
    private function getHandle(): SplFileObject {
        return $this->handle ??= $this->getFile()->openFile(StreamWrapperInterface::MODE_OPEN_READONLY);
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
        $this->handle = null;
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
        return $this->read(PHP_INT_MAX);
    }
    
    private ?int $size = null;
    
    /**
     * @return int|null
     */
    public function getSize(): ?int {
        return $this->size ??= $this->getFile()->getSize();
    }
    
    /**
     * @return int
     */
    public function tell(): int {
        return $this->handle === null ? 0 : $this->handle->ftell();
    }
    
    /**
     * @return bool
     */
    public function eof(): bool {
        return $this->handle === null ? $this->getSize() === 0 : $this->handle->eof();
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
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {
        if ($this->handle === null and $offset === 0) {
            return;
        }
        
        $this->getHandle()->fseek($offset, $whence);
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
        throw new BadMethodCallException('Cannot write a LazyFileWriterStream.');
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
        if ($length === 0) {
            return '';
        }
        
        return $this->getHandle()->fread(min($length, $this->getSize() + 1));
    }
}
