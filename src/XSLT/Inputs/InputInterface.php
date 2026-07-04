<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Inputs;

use DOMDocument;
use SplFileInfo;

/**
 * XSLT input that can be materialized as either a file or DOM document.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
interface InputInterface {
    
    public function toFile(): SplFileInfo;
    
    public function toDocument(): DOMDocument;
}
