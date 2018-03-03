<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use Slothsoft\Core\IO\HTTPFile;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface FileWriterInterface
{

    public function toFile(): HTTPFile;

    public function toString(): string;
}
