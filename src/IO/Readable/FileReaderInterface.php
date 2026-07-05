<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Readable;

use SplFileInfo;

/**
 * Reader that imports state from files or raw strings.
 *
 * @author Daniel Schulz
 * @since 2018-03-03
 */
interface FileReaderInterface {
    
    /**
     * @param SplFileInfo $sourceFile
     * @return void
     */
    public function fromFile(SplFileInfo $sourceFile): void;
    
    /**
     * @param string $sourceString
     * @return void
     */
    public function fromString(string $sourceString): void;
}
