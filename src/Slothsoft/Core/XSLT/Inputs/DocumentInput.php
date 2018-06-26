<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;

use Slothsoft\Core\IO\FileInfoFactory;
use DOMDocument;
use SplFileInfo;

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

    public function toFile(): SplFileInfo
    {
        if ($this->contentFile === null) {
            $this->contentFile = FileInfoFactory::createFromDocument($this->content);
        }
        return $this->contentFile;
    }

    public function toDocument(): DOMDocument
    {
        return $this->content;
    }
}

