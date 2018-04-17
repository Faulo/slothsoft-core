<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

use Slothsoft\Core\IO\HTTPFile;

class FileStreamWrapper extends ResourceStreamWrapper
{

    public function __construct(HTTPFile $file)
    {
        parent::__construct(fopen($file->getPath(), StreamWrapperInterface::MODE_OPEN_READONLY));
    }
}