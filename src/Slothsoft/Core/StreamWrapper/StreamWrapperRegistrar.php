<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

class StreamWrapperRegistrar implements StreamWrapperInterface
{

    private static $factories = [];

    private static function addFactory(string $scheme, StreamWrapperFactoryInterface $factory)
    {
        self::$factories[$scheme] = $factory;
    }

    private static function getFactoryByScheme(string $scheme): StreamWrapperFactoryInterface
    {
        return self::$factories[$scheme] ?? null;
    }

    private static function getFactoryByUrl(string $url): StreamWrapperFactoryInterface
    {
        return self::getFactoryByScheme(parse_url($url, PHP_URL_SCHEME));
    }

    public static function registerStreamWrapper(string $scheme, StreamWrapperFactoryInterface $factory)
    {
        self::addFactory($scheme, $factory);
        stream_wrapper_register($scheme, self::class);
    }

    public function url_stat(string $url, int $flags)
    {
        return self::getFactoryByUrl($url)->statUrl($url, $flags);
    }

    public function stream_open(string $path, string $mode, int $options, &$opened_path)
    {
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

    // StreamWrapperInterface
    public function stream_stat(): array
    {
        return $this->stream->stream_stat();
    }

    public function stream_eof(): bool
    {
        return $this->stream->stream_eof();
    }

    public function stream_seek(int $offset, int $whence): int
    {
        return $this->stream->stream_seek($offset, $whence);
    }

    public function stream_read(int $count)
    {
        return $this->stream->stream_read($count);
    }

    public function stream_write(string $data)
    {
        return $this->stream->stream_write($data);
    }
    
    public function stream_tell()
    {
        return $this->stream->stream_tell();
    }
    
    public function stream_close() : bool
    {
        return $this->stream->stream_close();
    }
}

