<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use SplFileInfo;


/**
 *
 * @author Daniel Schulz
 *        
 */
interface FileReaderInterface
{

    public function fromFile(SplFileInfo $sourceFile);

    public function fromString(string $sourceString);
}

