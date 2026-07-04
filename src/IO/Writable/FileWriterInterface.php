<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable;

use SplFileInfo;

/**
 * Writer that materializes object state as a file.
 *
 * @author Daniel Schulz
 * @since 2018-03-03
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
