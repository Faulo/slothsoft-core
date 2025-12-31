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
    
    /**
     * Converts the object's data to a file on disk.
     * Subsequent calls are expected to return the same file object each time.
     *
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo;
}

