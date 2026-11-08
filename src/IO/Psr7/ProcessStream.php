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
    
    /** @var resource|null */
    private $handle;
    
    /**
     * @param string $command
     */
    public function __construct(string $command) {
        $this->command = $command;
    }
    
    /** @return resource */
    private function getHandle() {
        $this->init();
        if ($this->handle === null) {
            throw new RuntimeException('Process stream is closed.');
        }
        return $this->handle;
    }
    
    private function init(): void {
        if ($this->handle !== null) {
            return;
        }
        
        $handle = popen($this->command, StreamWrapperInterface::MODE_OPEN_READONLY);
        if ($handle === false) {
            $this->handle = null;
            throw new RuntimeException(sprintf('Failed to open process "%s".', $this->command));
        }
        $this->handle = $handle;
    }
    
    /**
     * @return bool
     */
    public function eof(): bool {
        $this->init();
        
        return feof($this->getHandle());
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
    public function close() {
        if ($this->handle !== null) {
            pclose($this->handle);
            $this->handle = null;
        }
    }
    
    /**
     * @return ?resource
     */
    public function detach() {
        $ret = $this->handle;
        $this->handle = null;
        return $ret;
    }
    
    /**
     * @param ?string $key
     * @return ?array<mixed>
     */
    public function getMetadata(?string $key = null): ?array {
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
     * @return string
     */
    public function __toString(): string {
        return $this->getContents();
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
        throw new BadMethodCallException('Cannot tell a ProcessStream.');
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
     * @throws RuntimeException
     */
    public function read(int $length): string {
        $this->init();
        
        if ($length <= 0) {
            return '';
        }
        
        $result = fread($this->getHandle(), min($length, self::CHUNK_SIZE));
        if ($result === false) {
            throw new RuntimeException('Cannot read process output.');
        }
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
     * @param string $string
     * @return int
     * @throws BadMethodCallException
     */
    public function write(string $string): int {
        throw new BadMethodCallException('Cannot write a ProcessStream.');
    }
}
