<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

class StreamWrapperRegistrar implements StreamWrapperInterface {
    
    private static $factories = [];
    
    private static function addFactory(string $scheme, StreamWrapperFactoryInterface $factory) {
        self::$factories[$scheme] = $factory;
    }
    
    private static function getFactoryByScheme(string $scheme): ?StreamWrapperFactoryInterface {
        return self::$factories[$scheme] ?? null;
    }
    
    private static function getFactoryByUrl(string $url): ?StreamWrapperFactoryInterface {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        return $scheme ? self::getFactoryByScheme($scheme) : null;
    }
    
    public static function registerStreamWrapper(string $scheme, StreamWrapperFactoryInterface $factory) {
        self::addFactory($scheme, $factory);
        stream_wrapper_register($scheme, self::class);
    }
    
    /**
     *
     * @param string $url
     * @param int $flags
     * @return boolean|array
     * @see https://www.php.net/manual/de/streamwrapper.url-stat.php
     */
    public function url_stat(string $url, int $flags) {
        $factory = self::getFactoryByUrl($url);
        return $factory ? $factory->statUrl($url, $flags) : false;
    }
    
    private ?StreamWrapperInterface $stream = null;
    
    public $context;
    
    /**
     *
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param ?string $opened_path
     * @return boolean
     * @see https://www.php.net/manual/de/streamwrapper.stream-open.php
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path) {
        $this->stream = self::getFactoryByUrl($path)->createStreamWrapper($path, $mode, $options);
        
        if ($this->stream === null) {
            return false;
        }
        
        switch ($mode[0]) {
            case 'r':
            case 'w':
            case 'x':
            case 'c':
                $this->stream->stream_seek(0, SEEK_SET);
                break;
            case 'a':
                $this->stream->stream_seek(0, SEEK_END);
                break;
            default:
                return false;
        }
        
        return true;
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_stat()
     */
    public function stream_stat(): array {
        return $this->stream->stream_stat();
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_eof()
     */
    public function stream_eof(): bool {
        return $this->stream->stream_eof();
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_seek()
     */
    public function stream_seek(int $offset, int $whence): int {
        return $this->stream->stream_seek($offset, $whence);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_read()
     */
    public function stream_read(int $count) {
        return $this->stream->stream_read($count);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_write()
     */
    public function stream_write(string $data) {
        return $this->stream->stream_write($data);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_tell()
     */
    public function stream_tell() {
        return $this->stream->stream_tell();
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\StreamWrapper\StreamWrapperInterface::stream_close()
     */
    public function stream_close(): bool {
        return $this->stream->stream_close();
    }
}

