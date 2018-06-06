<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use DOMDocument;
use DOMDocumentFragment;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface DOMReaderInterface
{

    public function fromDocument(DOMDocument $sourceDoc);

    public function fromDocumentFragment(DOMDocumentFragment $sourceFragment);
}

