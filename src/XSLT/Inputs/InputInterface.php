<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;

use DOMDocument;
use SplFileInfo;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface InputInterface {

    public function toFile(): SplFileInfo;

    public function toDocument(): DOMDocument;
}

