<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\IO\HTTPFile;

/**
 *
 * @author Daniel Schulz
 *        
 */
trait FileWriterFileFromStringTrait {

    public function toFile(): HTTPFile
    {
        return HTTPFile::createFromString($this->toString());
    }
}

