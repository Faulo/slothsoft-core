<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use Slothsoft\Core\IO\FileInfoFactory;
use DOMDocument;
use SplFileInfo;
use XSLTProcessor;

/**
 *
 * @author Daniel Schulz
 *        
 */
class XsltProcessorAdapter extends GenericAdapter
{

    public function writeFile(SplFileInfo $outputFile = null): SplFileInfo
    {
        if (! $outputFile) {
            $outputFile = FileInfoFactory::createTempFile();
        }
        
        $xslt = new XSLTProcessor();
        $xslt->setParameter(null, $this->param);
        
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($this->template->toDocument());
        
        $xslt->transformToUri($this->source->toDocument(), (string) $outputFile);
        
        return $outputFile;
    }

    public function writeDocument(): DOMDocument
    {
        $xslt = new XSLTProcessor();
        foreach ($this->param as $key => $val) {
            $xslt->setParameter(null, $key, $val);
        }
        
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($this->template->toDocument());
        
        return $xslt->transformToDoc($this->source->toDocument());
    }
}

