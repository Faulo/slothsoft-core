<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use BadMethodCallException;
use SplFileInfo;
use SplFileObject;

final class LazyFileWriterStream implements StreamInterface {
    
    public function __construct(FileWriterInterface $writer) {
        $this->writer = $writer;
    }
    
    private ?FileWriterInterface $writer;
    
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
    
    public function __toString() {
        $this->rewind();
        return $this->getContents();
    }
    
    public function close() {
        $this->writer = null;
        $this->handle = null;
    }
    
    public function detach() {
        $this->close();
        return null;
    }
    
    public function getMetadata($key = null) {
        return $key === null ? [] : null;
    }
    
    public function getContents() {
        return $this->read(PHP_INT_MAX);
    }
    
    private ?int $size = null;
    
    public function getSize() {
        return $this->size ??= $this->getFile()->getSize();
    }
    
    public function tell() {
        return $this->handle === null ? 0 : $this->handle->ftell();
    }
    
    public function eof() {
        return $this->handle === null ? $this->getSize() === 0 : $this->handle->eof();
    }
    
    public function isSeekable() {
        return true;
    }
    
    public function seek($offset, $whence = SEEK_SET) {
        if ($this->handle === null and $offset === 0) {
            return;
        }
        
        $this->getHandle()->fseek($offset, $whence);
    }
    
    public function rewind() {
        $this->seek(0, SEEK_SET);
    }
    
    public function isWritable() {
        return false;
    }
    
    public function write($string) {
        throw new BadMethodCallException('Cannot write a LazyFileWriterStream.');
    }
    
    public function isReadable() {
        return true;
    }
    
    public function read($length) {
        if ($length === 0) {
            return '';
        }
        
        return $this->getHandle()->fread(min($length, $this->getSize() + 1));
    }
}
