<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Inputs;

use DOMDocument;
use Slothsoft\Core\DOMHelper;
use SplFileInfo;

/**
 * File-backed XSLT input with lazy DOM loading.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
final class FileInput implements InputInterface {
    
    private SplFileInfo $content;
    
    private ?DOMDocument $contentDocument = null;
    
    public function __construct(SplFileInfo $input) {
        $this->content = $input;
    }
    
    public function toFile(): SplFileInfo {
        return $this->content;
    }
    
    public function toDocument(): DOMDocument {
        if ($this->contentDocument === null) {
            $this->contentDocument = DOMHelper::loadDocument((string) $this->content);
        }
        return $this->contentDocument;
    }
}
