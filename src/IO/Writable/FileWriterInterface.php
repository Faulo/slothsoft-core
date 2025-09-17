<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use SplFileInfo;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface FileWriterInterface {
    
    public function toFile(): SplFileInfo;
}

