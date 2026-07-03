<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

final class ProcessStream implements StreamInterface {
    
    private const CHUNK_SIZE = 8192;
    
    private string $command;
    
    private $handle;
    
    public function __construct(string $command) {
        $this->command = $command;
    }
    
    private function init(): void {
        if ($this->handle !== null) {
            return;
        }
        
        $this->handle = popen($this->command, StreamWrapperInterface::MODE_OPEN_READONLY);
        if ($this->handle === false) {
            $this->handle = null;
            throw new RuntimeException(sprintf('Failed to open process "%s".', $this->command));
        }
    }
    
    public function eof() {
        $this->init();
        
        return feof($this->handle);
    }
    
    public function rewind() {
        $this->seek(0);
    }
    
    public function close() {
        if ($this->handle !== null) {
            pclose($this->handle);
            $this->handle = null;
        }
    }
    
    public function detach() {
        $ret = $this->handle;
        $this->handle = null;
        return $ret;
    }
    
    public function getMetadata($key = null) {
        return $key === null ? [] : null;
    }
    
    public function getContents() {
        $ret = '';
        while (! $this->eof()) {
            $ret .= $this->read(self::CHUNK_SIZE);
        }
        return $ret;
    }
    
    public function __toString() {
        return $this->getContents();
    }
    
    public function getSize() {
        return null;
    }
    
    public function tell() {
        throw new BadMethodCallException('Cannot tell a ProcessStream.');
    }
    
    public function isReadable() {
        return true;
    }
    
    public function read($length) {
        $this->init();
        
        if ($length <= 0) {
            return '';
        }
        
        return fread($this->handle, min($length, self::CHUNK_SIZE));
    }
    
    public function isSeekable() {
        return false;
    }
    
    public function seek($offset, $whence = SEEK_SET) {
        if ($offset === 0 and $whence === SEEK_SET) {
            $this->init();
        } else {
            throw new BadMethodCallException('Cannot seek a ProcessStream.');
        }
    }
    
    public function isWritable() {
        return false;
    }
    
    public function write($string) {
        throw new BadMethodCallException('Cannot write a ProcessStream.');
    }
}
