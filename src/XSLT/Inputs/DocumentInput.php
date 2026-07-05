<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Inputs;

use DOMDocument;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

/**
 * DOM-backed XSLT input with lazy temporary file materialization.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
final class DocumentInput implements InputInterface {
    
    private DOMDocument $content;
    
    private ?SplFileInfo $contentFile = null;
    
    /**
     * @param DOMDocument $input
     */
    public function __construct(DOMDocument $input) {
        $this->content = $input;
    }
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        if ($this->contentFile === null) {
            $this->contentFile = FileInfoFactory::createFromDocument($this->content);
        }
        return $this->contentFile;
    }
    
    /**
     * @return DOMDocument
     */
    public function toDocument(): DOMDocument {
        return $this->content;
    }
}
