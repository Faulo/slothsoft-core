<?php
namespace Slothsoft\Core\XSLT\Inputs;

use Slothsoft\Farah\HTTPFile;
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

