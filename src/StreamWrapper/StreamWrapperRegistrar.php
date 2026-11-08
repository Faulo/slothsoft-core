<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use LogicException;

final class StreamWrapperRegistrar implements StreamWrapperInterface {
    
    /** @var array<mixed> */
    private static array $factories = [];
    
    /** @return void */
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
    
    /**
     * @param string $scheme
     * @param StreamWrapperFactoryInterface $factory
     * @return void
     * @throws LogicException
     */
    public static function registerStreamWrapper(string $scheme, StreamWrapperFactoryInterface $factory) {
        if (isset(self::$factories[$scheme])) {
            throw new LogicException("Scheme '$scheme' has already been registered to a factory.");
        }
        
        self::addFactory($scheme, $factory);
        stream_wrapper_register($scheme, self::class);
    }
    
    /**
     *
     * @param string $url
     * @param int $flags
     * @return boolean|array<mixed>
     * @see https://www.php.net/manual/de/streamwrapper.url-stat.php
     */
    
    public function url_stat(string $url, int $flags) {
        $factory = self::getFactoryByUrl($url);
        return $factory ? $factory->statUrl($url, $flags) : false;
    }
    
    private ?StreamWrapperInterface $stream = null;
    
    private function getStream(): StreamWrapperInterface {
        if ($this->stream === null) {
            throw new LogicException('The stream is not open.');
        }
        return $this->stream;
    }
    
    /** @var resource|null */
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
    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool {
        $factory = self::getFactoryByUrl($path);
        if ($factory === null) {
            return false;
        }
        $this->stream = $factory->createStreamWrapper($path, $mode, $options);
        
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
     * @return array<mixed>
     * @see StreamWrapperInterface::stream_stat
     */
    public function stream_stat(): array {
        return $this->getStream()->stream_stat();
    }
    
    /**
     *
     * {@inheritdoc}
     * @return bool
     * @see StreamWrapperInterface::stream_eof
     */
    public function stream_eof(): bool {
        return $this->getStream()->stream_eof();
    }
    
    /**
     *
     * {@inheritdoc}
     * @param int $offset
     * @param int $whence
     * @return int
     * @see StreamWrapperInterface::stream_seek
     */
    public function stream_seek(int $offset, int $whence): int {
        return $this->getStream()->stream_seek($offset, $whence);
    }
    
    /**
     *
     * {@inheritdoc}
     * @param int $count
     * @return string|false
     * @see StreamWrapperInterface::stream_read
     */
    public function stream_read(int $count) {
        return $this->getStream()->stream_read($count);
    }
    
    /**
     *
     * {@inheritdoc}
     * @param string $data
     * @return int|false
     * @see StreamWrapperInterface::stream_write
     */
    public function stream_write(string $data) {
        return $this->getStream()->stream_write($data);
    }
    
    /**
     *
     * {@inheritdoc}
     * @return int|false
     * @see StreamWrapperInterface::stream_tell
     */
    public function stream_tell() {
        return $this->getStream()->stream_tell();
    }
    
    /**
     *
     * {@inheritdoc}
     * @return bool
     * @see StreamWrapperInterface::stream_close
     */
    public function stream_close(): bool {
        return $this->getStream()->stream_close();
    }
}
