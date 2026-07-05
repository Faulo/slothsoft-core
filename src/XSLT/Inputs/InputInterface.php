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
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo;
    
    /**
     * @return DOMDocument
     */
    public function toDocument(): DOMDocument;
}
