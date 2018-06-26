<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait FileWriterFileFromStringTrait {

    public function toFile(): SplFileInfo
    {
        return FileInfoFactory::createFromString($this->toString());
    }
}

