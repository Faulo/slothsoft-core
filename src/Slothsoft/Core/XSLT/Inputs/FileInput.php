<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;

use DOMDocument;
use SplFileInfo;
use Slothsoft\Core\DOMHelper;

/**
 *
 * @author Daniel Schulz
 *        
 */
class FileInput implements InputInterface {

    private $content;

    private $contentDocument;

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

