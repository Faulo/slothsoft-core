<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface DOMWriterInterface
{

    public function toElement(DOMDocument $targetDoc): DOMElement;

    public function toDocument(): DOMDocument;
}

