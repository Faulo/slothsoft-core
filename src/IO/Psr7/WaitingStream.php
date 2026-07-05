<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use BadMethodCallException;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

final class WaitingStream implements StreamInterface {
    use StreamDecoratorTrait;
    
    private StreamInterface $stream;
    
    private int $usleep;
    
    private ?array $heartbeat;
    
    /**
     * @param StreamInterface $stream
     * @param int $waitInMicroseconds
     * @param ?array $heartbeat
     */
    public function __construct(StreamInterface $stream, int $waitInMicroseconds, ?array $heartbeat = null) {
        $this->stream = $stream;
        $this->usleep = $waitInMicroseconds;
        $this->heartbeat = $heartbeat;
    }
    
    /**
     * @return string
     */
    public function __toString(): string {
        return $this->getContents();
    }
    
    /**
     * @return void
     */
    public function close(): void {
        $this->stream->close();
    }
    
    /**
     * @return ?resource
     */
    public function detach() {
        return $this->stream->detach();
    }
    
    /**
     * @return ?int
     */
    public function getSize(): ?int {
        return $this->stream->getSize();
    }
    
    /**
     * @return int
     */
    public function tell(): int {
        return $this->stream->tell();
    }
    
    /**
     * @return bool
     */
    public function eof(): bool {
        return $this->stream->eof();
    }
    
    /**
     * @return string
     */
    public function getContents(): string {
        $buffer = '';
        while (! $this->eof()) {
            $buffer .= $this->read(8192);
        }
        return $buffer;
    }
    
    /**
     * @param ?string $key
     * @return array|mixed|null
     */
    public function getMetadata(?string $key = null) {
        return $this->stream->getMetadata($key);
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
        $timeWaited = 0;
        while (! $this->stream->eof()) {
            $content = $this->stream->read($length);
            if ($content !== '') {
                return $content;
            }
            usleep($this->usleep);
            
            if ($this->heartbeat) {
                $timeWaited += $this->usleep;
                if ($timeWaited > $this->heartbeat['interval']) {
                    return $this->heartbeat['content'];
                }
            }
        }
        return '';
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
        throw new BadMethodCallException('Cannot seek a WaitingStream.');
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
        throw new BadMethodCallException('Cannot write a WaitingStream.');
    }
}
