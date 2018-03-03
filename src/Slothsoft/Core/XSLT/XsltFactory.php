<?php
namespace Slothsoft\Core\XSLT;

use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\XSLT\Adapters\AdapterInterface;
use Slothsoft\Core\XSLT\Adapters\CliAdapter;
use Slothsoft\Core\XSLT\Adapters\SaxonProcessorAdapter;
use Slothsoft\Core\XSLT\Adapters\XsltProcessorAdapter;
use Slothsoft\Core\XSLT\Inputs\DocumentInput;
use Slothsoft\Core\XSLT\Inputs\FileInput;
use Slothsoft\Core\XSLT\Inputs\InputInterface;
use DOMDocument;
use DomainException;

/**
 *
 * @author Daniel Schulz
 *        
 */
class XsltFactory
{

    public static function createAdapter(float $xsltVersion): AdapterInterface
    {
        $xsltVersion = sprintf('%1.1f', $xsltVersion);
        if (isset(CORE_XSLT_PROCESSORS[$xsltVersion])) {
            $name = CORE_XSLT_PROCESSORS[$xsltVersion];
            switch ($name) {
                case CORE_XSLT_PHP:
                    return new XsltProcessorAdapter();
                case CORE_XSLT_LIBXML:
                    return new CliAdapter(CORE_XSLT_LIBXML_PATH, CORE_XSLT_LIBXML_ARGS);
                case CORE_XSLT_EXSELT:
                    return new CliAdapter(CORE_XSLT_EXSELT_PATH, CORE_XSLT_EXSELT_ARGS);
                case CORE_XSLT_SAXON8:
                    return new CliAdapter(CORE_XSLT_SAXON8_PATH, CORE_XSLT_SAXON8_ARGS);
                case CORE_XSLT_SAXON9:
                    return new CliAdapter(CORE_XSLT_SAXON9_PATH, CORE_XSLT_SAXON9_ARGS);
                case CORE_XSLT_SAXONC:
                    return new SaxonProcessorAdapter();
                default:
                    throw new DomainException("xslt processor '$name' is not supported by this implementation");
            }
        }
        throw new DomainException("xslt version '$xsltVersion' is not supported by your configuration (add it to CORE_XSLT_PROCESSORS)");
    }

    public static function createInput($input): InputInterface
    {
        switch (true) {
            case is_string($input):
                return new FileInput(HTTPFile::createFromPath($input));
            case $input instanceof DOMDocument:
                return new DocumentInput($input);
            case $input instanceof DOMWriterInterface:
                return new DocumentInput($input->toDocument());
            case $input instanceof HTTPFile:
                return new FileInput($input);
            case $input instanceof FileWriterInterface:
                return new FileInput($input->toFile());
            case is_object($input):
                throw new DomainException("input class not supported: " . get_class($input));
            default:
                throw new DomainException("input type not supported: " . gettype($input));
        }
    }
}

