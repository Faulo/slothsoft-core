<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

class ResourceStreamWrapper implements StreamWrapperInterface {
    
    private $handle;
    
    /**
     * @param mixed $resource
     * @return void
     */
    public function __construct($resource) {
        $this->handle = $resource;
    }
    
    /**
     * @return array
     */
    public function stream_stat(): array {
        return fstat($this->handle);
    }
    
    /**
     * @return bool
     */
    public function stream_eof(): bool {
        return feof($this->handle);
    }
    
    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): int {
        return fseek($this->handle, $offset, $whence);
    }
    
    /**
     * @param int $count
     * @return string|false
     */
    public function stream_read(int $count) {
        return fread($this->handle, $count);
    }
    
    /**
     * @param string $data
     * @return int|false
     */
    public function stream_write(string $data) {
        return fwrite($this->handle, $data);
    }
    
    /**
     * @return int|false
     */
    public function stream_tell() {
        return ftell($this->handle);
    }
    
    /**
     * @return bool
     */
    public function stream_close(): bool {
        // let's not tho.
        return true;
    }
}
