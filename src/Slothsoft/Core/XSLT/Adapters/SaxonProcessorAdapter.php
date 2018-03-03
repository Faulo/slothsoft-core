<?php
namespace Slothsoft\Core\XSLT\Adapters;

use Slothsoft\Farah\HTTPFile;
use DOMDocument;

/**
 *
 * @author Daniel Schulz
 *        
 */
class SaxonProcessorAdapter extends GenericAdapter
{

    private function newSaxonProcessor()
    {
        return new \Saxon\SaxonProcessor();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()
     *
     */
    public function writeFile(HTTPFile $outputFile = null): HTTPFile
    {
        if (! $outputFile) {
            $outputFile = HTTPFile::createFromTemp();
        }
        
        $saxon = $this->newSaxonProcessor();
        $xslt = $saxon->newXsltProcessor();
        
        $xslt->setSourceFromFile($this->source->toFile()
            ->getPath());
        $xslt->compileFromFile($this->template->toFile()
            ->getPath());
        $xslt->setOutputFile($outputFile->getPath());
        
        $xslt->transformToFile();
        
        return $outputFile;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toDocument()
     *
     */
    public function writeDocument(): DOMDocument
    {
        return $this->writeFile()->getDocument();
    }
}
