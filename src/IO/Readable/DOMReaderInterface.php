<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface DOMReaderInterface {

    public function fromDocument(DOMDocument $sourceDoc): void;

    public function fromElement(DOMElement $sourceElement): void;
}

