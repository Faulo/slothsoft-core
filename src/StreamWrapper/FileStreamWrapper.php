<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use SplFileInfo;

final class FileStreamWrapper extends ResourceStreamWrapper {
    
    /**
     * @param SplFileInfo $file
     * @throws \RuntimeException
     */
    public function __construct(SplFileInfo $file) {
        parent::__construct(\GuzzleHttp\Psr7\Utils::tryFopen((string) $file, StreamWrapperInterface::MODE_OPEN_READONLY));
    }
}
