<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use Slothsoft\Core\IO\HTTPFile;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface FileReaderInterface
{

    public function fromFile(HTTPFile $sourceFile);

    public function fromString(string $sourceString);
}

