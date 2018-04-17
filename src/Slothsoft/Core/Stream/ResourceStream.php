<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Stream;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class ResourceStream implements StreamInterface
{
    private $resource;
    
    private $seekable;
    private $usable;
    private $metadata;
    
    public function __construct($resource, $isSeekable = true)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('PHP resource expected, but got ' . gettype($resource));
        }
        $this->resource = $resource;
        $this->usable = true;
        $this->seekable = $isSeekable;
    }
    
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }
    
    public function close()
    {
        if ($this->usable) {
            fclose($this->resource);
        }
        $this->detach();
    }
    
    public function detach()
    {
        $this->usable = false;
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }
    
    public function getSize()
    {
        return null;
    }
    
    public function tell()
    {
        if ($this->usable) {
            $position = ftell($this->resource);
            if ($position !== false) {
                return $position;
            }
        }
        throw new RuntimeException(__METHOD__ . ' failed');
    }
    
    public function eof()
    {
        return $this->usable
            ? feof($this->resource)
            : true;
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if ($this->usable and $this->seekable) {
            if (fseek($this->resource, $offset, $whence) === 0) {
                return;
            }
        }
        throw new RuntimeException(__METHOD__ . ' failed');
    }
    
    public function rewind()
    {
        if ($this->seekable) {
            $this->seek(0);
            return;
        }
        throw new RuntimeException(__METHOD__ . ' failed');
    }
    
    public function isWritable() {
        return $this->usable;
    }
    
    public function write($string)
    {
        if ($this->usable) {
            $length = fwrite($this->resource, $string);
            if ($length !== false) {
                return $length;
            }
        }
        throw new RuntimeException(__METHOD__ . ' failed');
    }
    
    public function isReadable() {
        return $this->usable;
    }

    public function read($length)
    {
        if ($this->usable) {
            $data = fread($this->resource, $length);
            if ($data !== false) {
                return $data;
            }
        }
        throw new RuntimeException(__METHOD__ . ' failed');
    }
    
    public function getContents()
    {
        if ($this->usable) {
            $data = stream_get_contents($this->resource);
            if ($data !== false) {
                return $data;
            }
        }
        throw new RuntimeException(__METHOD__ . ' failed');
    }
    
    public function getMetadata($key = null)
    {
        if ($this->metadata === null) {
            $this->metadata = $this->hasResource
                ? stream_get_meta_data($this->resource)
                : [];
        }
        return $key === null
            ? $this->metadata
            : $this->metadata[$key] ?? null;
    }
}
