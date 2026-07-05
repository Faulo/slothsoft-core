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
     * @param array|null $heartbeat
     * @return void
     */
    public function __construct(StreamInterface $stream, int $waitInMicroseconds, ?array $heartbeat = null) {
        $this->stream = $stream;
        $this->usleep = $waitInMicroseconds;
        $this->heartbeat = $heartbeat;
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
     * @param mixed $offset
     * @param mixed $whence
     * @return void
     * @throws BadMethodCallException
     */
    public function seek($offset, $whence = SEEK_SET) {
        throw new BadMethodCallException('Cannot seek a WaitingStream.');
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
        throw new BadMethodCallException('Cannot write a WaitingStream.');
    }
}
