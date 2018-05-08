<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

interface StreamWrapperInterface
{

    const MODE_OPEN_READONLY = 'rb';

    const MODE_OPEN_READWRITE = 'r+b';

    const MODE_CREATE_WRITEONLY = 'wb';

    const MODE_CREATE_READWRITE = 'w+b';

    const MODE_APPEND_WRITEONLY = 'ab';

    const MODE_APPEND_READWRITE = 'a+b';

    /**
     *
     * @return array
     * @see http://php.net/manual/de/function.fstat.php
     */
    public function stream_stat(): array;

    /**
     *
     * @param int $count
     * @return string|false
     * @see http://php.net/manual/de/function.fread.php
     */
    public function stream_read(int $count);

    /**
     *
     * @return int|false
     * @see http://php.net/manual/de/function.ftell.php
     */
    public function stream_tell();

    /**
     *
     * @return bool
     * @see http://php.net/manual/de/function.feof.php
     */
    public function stream_eof(): bool;

    /**
     *
     * @param int $offset
     * @param int $whence
     * @return "0"|"-1"
     * @see @see http://php.net/manual/de/function.fseek.php
     */
    public function stream_seek(int $offset, int $whence): int;

    /**
     *
     * @param string $data
     * @return int|false
     * @see http://php.net/manual/de/function.fwrite.php
     */
    public function stream_write(string $data);
    
    /**
     * @return bool
     * @see http://php.net/manual/de/function.fclose.php
     */
    public function stream_close() : bool;
}

