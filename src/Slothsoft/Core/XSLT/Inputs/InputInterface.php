<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;

use Slothsoft\Core\IO\HTTPFile;
use DOMDocument;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface InputInterface
{

    public function toFile(): HTTPFile;

    public function toDocument(): DOMDocument;
}

