<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Psr7StreamWrapper implements StreamWrapperInterface {

    private $stream;

    public function __construct(StreamInterface $stream) {
        $this->stream = $stream;
    }

    public function stream_stat(): array {
        return [];
    }

    public function stream_eof(): bool {
        return $this->stream->eof();
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): int {
        try {
            $this->stream->seek($offset, $whence);
            return 0;
        } catch (RuntimeException $e) {
            return - 1;
        }
    }

    public function stream_read(int $count) {
        try {
            return $this->stream->read($count);
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function stream_write(string $data) {
        try {
            return $this->stream->write($data);
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function stream_tell() {
        try {
            $this->stream->tell();
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function stream_close(): bool {
        try {
            $this->stream->close();
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }
}