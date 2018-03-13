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
class FileInput extends GenericInput
{

    private $content;

    private $contentDocument;

    public function __construct(HTTPFile $input)
    {
        $this->content = $input;
    }

    public function toFile(): HTTPFile
    {
        return $this->content;
    }

    public function toDocument(): DOMDocument
    {
        if ($this->contentDocument === null) {
            $this->contentDocument = $this->content->getDocument();
        }
        return $this->contentDocument;
    }
}

