<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use BadMethodCallException;

class GeneratorStream implements StreamInterface {

    private const NEW = 1;

    private const START = 2;

    private const MIDDLE = 3;

    private const END = 4;

    private $writer;

    private $generator;

    private $state;

    public function __construct(ChunkWriterInterface $writer) {
        $this->writer = $writer;
        $this->state = self::NEW;
    }

    private function init() {
        if ($this->state === self::NEW) {
            $this->generator = $this->writer->toChunks();
            $this->state = self::START;
        }
    }

    public function eof() {
        $this->init();
        return ! $this->generator->valid();
    }

    public function rewind() {
        $this->seek(0);
    }

    public function close() {
        $this->writer = null;
        $this->generator = null;
        $this->state = self::END;
    }

    public function detach() {
        $this->writer = null;
        $this->generator = null;
        $this->state = self::END;
    }

    public function getMetadata($key = null) {
        return $key === null ? [] : null;
    }

    public function getContents() {
        $ret = '';
        while (! $this->eof()) {
            $ret .= $this->read(PHP_INT_MAX);
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
        throw new BadMethodCallException('Cannot tell a GeneratorStream.');
    }

    public function isReadable() {
        return true;
    }

    public function read($length) {
        $this->init();
        if ($this->state === self::START) {
            $this->state = self::MIDDLE;
        } else {
            $this->generator->next();
        }
        return $this->generator->valid() ? (string) $this->generator->current() : '';
    }

    public function isSeekable() {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET) {
        if ($offset === 0 and $whence === SEEK_SET) {
            if ($this->state === self::MIDDLE) {
                $this->generator->rewind();
                $this->state = self::START;
            }
        } else {
            throw new BadMethodCallException('Cannot seek a GeneratorStream.');
        }
    }

    public function isWritable() {
        return false;
    }

    public function write($string) {
        throw new BadMethodCallException('Cannot write a GeneratorStream.');
    }
}