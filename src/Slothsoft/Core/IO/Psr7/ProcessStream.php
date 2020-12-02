<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use BadMethodCallException;

class ProcessStream implements StreamInterface {

    private $command;

    private $handle;

    public function __construct(string $command) {
        $this->command = $command;
    }

    private function init() {
        $this->handle = popen($this->command, StreamWrapperInterface::MODE_OPEN_READONLY);
    }

    public function eof() {
        return feof($this->handle);
    }

    public function rewind() {
        $this->seek(0);
    }

    public function close() {
        pclose($this->handle);
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
        throw new BadMethodCallException('Cannot tell a ProcessStream.');
    }

    public function isReadable() {
        return true;
    }

    public function read($length) {
        return fread($this->handle, $length);
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