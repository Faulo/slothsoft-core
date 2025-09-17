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
     * @param int $flags
     * @return array|false
     * @see http://php.net/manual/de/function.stat.php
     */
    public function statUrl(string $url, int $flags);
}

