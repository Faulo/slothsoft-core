<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

interface StreamWrapperInterface {
    
    const MODE_OPEN_READONLY = 'rb';
    
    const MODE_OPEN_READWRITE = 'r+b';
    
    const MODE_CREATE_WRITEONLY = 'wb';
    
    const MODE_CREATE_READWRITE = 'w+b';
    
    const MODE_APPEND_WRITEONLY = 'ab';
    
    const MODE_APPEND_READWRITE = 'a+b';
    
    /**
     *
     * @return array
     * @see https://www.php.net/manual/en/streamwrapper.stream-stat.php
     */
    public function stream_stat(): array;
    
    /**
     *
     * @param int $count
     * @return string|false
     * @see https://www.php.net/manual/en/streamwrapper.stream-read.php
     */
    public function stream_read(int $count);
    
    /**
     *
     * @return int|false
     * @see https://www.php.net/manual/en/streamwrapper.stream-tell.php
     */
    public function stream_tell();
    
    /**
     *
     * @return bool
     * @see https://www.php.net/manual/en/streamwrapper.stream-eof.php
     */
    public function stream_eof(): bool;
    
    /**
     *
     * @param int $offset
     * @param int $whence
     * @return int
     * @see https://www.php.net/manual/en/streamwrapper.stream-seek.php
     */
    public function stream_seek(int $offset, int $whence): int;
    
    /**
     *
     * @param string $data
     * @return int|false
     * @see https://www.php.net/manual/en/streamwrapper.stream-write.php
     */
    public function stream_write(string $data);
    
    /**
     *
     * @return bool
     * @see https://www.php.net/manual/en/streamwrapper.stream-close.php
     */
    public function stream_close(): bool;
}

