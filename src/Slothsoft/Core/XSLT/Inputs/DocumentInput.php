<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;

use Slothsoft\Core\IO\HTTPFile;
use DOMDocument;

/**
 *
 * @author Daniel Schulz
 *        
 */
class DocumentInput implements InputInterface
{

    private $content;

    private $contentFile;

    public function __construct(DOMDocument $input)
    {
        $this->content = $input;
    }

    public function toFile(): HTTPFile
    {
        if ($this->contentFile === null) {
            $this->contentFile = HTTPFile::createFromDocument($this->content);
        }
        return $this->contentFile;
    }

    public function toDocument(): DOMDocument
    {
        return $this->content;
    }
}

