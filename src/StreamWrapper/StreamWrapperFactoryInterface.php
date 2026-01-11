<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

interface StreamWrapperFactoryInterface {
    
    /**
     *
     * @param string $url
     * @param string $mode
     * @param int $options
     * @return StreamWrapperInterface|null
     */
    public function createStreamWrapper(string $url, string $mode, int $options);
    
    /**
     *
     * @param string $url
     *            The file path or URL to stat. Note that in the case of a URL, it must be a :// delimited URL. Other URL forms are not supported.
     * @param int $flags
     *            Holds additional flags set by the streams API. It can hold one or both of STREAM_URL_STAT_LINK and STREAM_URL_STAT_QUIET.
     * @return array|false
     * @see https://www.php.net/manual/de/streamwrapper.url-stat.php
     */
    public function statUrl(string $url, int $flags);
}

