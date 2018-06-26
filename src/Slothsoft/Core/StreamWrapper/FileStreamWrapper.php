<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

use SplFileInfo;

class FileStreamWrapper extends ResourceStreamWrapper
{

    public function __construct(SplFileInfo $file)
    {
        parent::__construct(fopen((string) $file, StreamWrapperInterface::MODE_OPEN_READONLY));
    }
}