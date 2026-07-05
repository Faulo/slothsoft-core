<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class Psr7StreamWrapper implements StreamWrapperInterface {
    
    private StreamInterface $stream;
    
    /**
     * @param StreamInterface $stream
     * @return void
     */
    public function __construct(StreamInterface $stream) {
        $this->stream = $stream;
    }
    
    /**
     * @return array
     */
    public function stream_stat(): array {
        return [];
    }
    
    /**
     * @return bool
     */
    public function stream_eof(): bool {
        return $this->stream->eof();
    }
    
    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): int {
        try {
            $this->stream->seek($offset, $whence);
            return 0;
        } catch (RuntimeException $e) {
            return -1;
        }
    }
    
    /**
     * @param int $count
     * @return string|false
     */
    public function stream_read(int $count) {
        try {
            return $this->stream->read($count);
        } catch (RuntimeException $e) {
            return false;
        }
    }
    
    /**
     * @param string $data
     * @return int|false
     */
    public function stream_write(string $data) {
        try {
            return $this->stream->write($data);
        } catch (RuntimeException $e) {
            return false;
        }
    }
    
    /**
     * @return int|false
     */
    public function stream_tell() {
        try {
            return $this->stream->tell();
        } catch (RuntimeException $e) {
            return false;
        }
    }
    
    /**
     * @return bool
     */
    public function stream_close(): bool {
        try {
            $this->stream->close();
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }
}
