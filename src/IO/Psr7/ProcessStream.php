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
    
    /**
     * @param string $command
     * @return void
     */
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
    
    /**
     * @return bool
     */
    public function eof(): bool {
        $this->init();
        
        return feof($this->handle);
    }
    
    /**
     * @return void
     */
    public function rewind() {
        $this->seek(0);
    }
    
    /**
     * @return void
     */
    public function close() {
        if ($this->handle !== null) {
            pclose($this->handle);
            $this->handle = null;
        }
    }
    
    /**
     * @return mixed
     */
    public function detach() {
        $ret = $this->handle;
        $this->handle = null;
        return $ret;
    }
    
    /**
     * @param mixed $key
     * @return array|null
     */
    public function getMetadata($key = null): ?array {
        return $key === null ? [] : null;
    }
    
    /**
     * @return string
     */
    public function getContents(): string {
        $ret = '';
        while (! $this->eof()) {
            $ret .= $this->read(self::CHUNK_SIZE);
        }
        return $ret;
    }
    
    /**
     * @return mixed
     */
    public function __toString() {
        return $this->getContents();
    }
    
    /**
     * @return mixed
     */
    public function getSize() {
        return null;
    }
    
    /**
     * @return void
     * @throws BadMethodCallException
     */
    public function tell() {
        throw new BadMethodCallException('Cannot tell a ProcessStream.');
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
        $this->init();
        
        if ($length <= 0) {
            return '';
        }
        
        return fread($this->handle, min($length, self::CHUNK_SIZE));
    }
    
    /**
     * @return bool
     */
    public function isSeekable(): bool {
        return false;
    }
    
    /**
     * @param mixed $offset
     * @param mixed $whence
     * @return void
     * @throws BadMethodCallException
     */
    public function seek($offset, $whence = SEEK_SET) {
        if ($offset === 0 and $whence === SEEK_SET) {
            $this->init();
        } else {
            throw new BadMethodCallException('Cannot seek a ProcessStream.');
        }
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
        throw new BadMethodCallException('Cannot write a ProcessStream.');
    }
}
