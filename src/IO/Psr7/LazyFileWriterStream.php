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
     * @return void
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
     * @return mixed
     */
    public function __toString() {
        $this->rewind();
        return $this->getContents();
    }
    
    /**
     * @return void
     */
    public function close() {
        $this->writer = null;
        $this->handle = null;
    }
    
    /**
     * @return mixed
     */
    public function detach() {
        $this->close();
        return null;
    }
    
    /**
     * @param mixed $key
     * @return array|null
     */
    public function getMetadata($key = null): ?array {
        return $key === null ? [] : null;
    }
    
    /**
     * @return mixed
     */
    public function getContents() {
        return $this->read(PHP_INT_MAX);
    }
    
    private ?int $size = null;
    
    /**
     * @return mixed
     */
    public function getSize() {
        return $this->size ??= $this->getFile()->getSize();
    }
    
    /**
     * @return mixed
     */
    public function tell() {
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
     * @param mixed $offset
     * @param mixed $whence
     * @return mixed
     */
    public function seek($offset, $whence = SEEK_SET) {
        if ($this->handle === null and $offset === 0) {
            return;
        }
        
        $this->getHandle()->fseek($offset, $whence);
    }
    
    /**
     * @return void
     */
    public function rewind() {
        $this->seek(0);
    }
    
    /**
     * @return bool
     */
    public function isWritable(): bool {
        return false;
    }
    
    /**
     * @param mixed $string
     * @return void
     * @throws BadMethodCallException
     */
    public function write($string) {
        throw new BadMethodCallException('Cannot write a LazyFileWriterStream.');
    }
    
    /**
     * @return bool
     */
    public function isReadable(): bool {
        return true;
    }
    
    /**
     * @param mixed $length
     * @return mixed
     */
    public function read($length) {
        if ($length === 0) {
            return '';
        }
        
        return $this->getHandle()->fread(min($length, $this->getSize() + 1));
    }
}
