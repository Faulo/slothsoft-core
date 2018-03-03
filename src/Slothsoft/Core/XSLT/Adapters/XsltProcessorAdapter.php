<?php
namespace Slothsoft\Core\XSLT\Adapters;

use Slothsoft\Core\IO\HTTPFile;
use DOMDocument;
use XSLTProcessor;

/**
 *
 * @author Daniel Schulz
 *        
 */
class XsltProcessorAdapter extends GenericAdapter
{

    public function writeFile(HTTPFile $outputFile = null): HTTPFile
    {
        if (! $outputFile) {
            $outputFile = HTTPFile::createFromTemp();
        }
        
        $xslt = new XSLTProcessor();
        $xslt->setParameter(null, $this->param);
        
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($this->template->toDocument());
        
        $xslt->transformToUri($this->source->toDocument(), $outputFile->getPath());
        
        return $outputFile;
    }

    public function writeDocument(): DOMDocument
    {
        $xslt = new XSLTProcessor();
        $xslt->setParameter(null, $this->param);
        
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($this->template->toDocument());
        
        return $xslt->transformToDoc($this->source->toDocument());
    }
}

