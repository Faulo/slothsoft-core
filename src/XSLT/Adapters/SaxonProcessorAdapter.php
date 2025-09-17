<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use Slothsoft\Core\IO\FileInfoFactory;
use DOMDocument;
use SplFileInfo;

/**
 *
 * @author Daniel Schulz
 *        
 */
class SaxonProcessorAdapter extends GenericAdapter {
    
    private function newSaxonProcessor() {
        return new \Saxon\SaxonProcessor();
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()
     *
     */
    public function writeFile(SplFileInfo $outputFile = null): SplFileInfo {
        if (! $outputFile) {
            $outputFile = FileInfoFactory::createTempFile();
        }
        
        $saxon = $this->newSaxonProcessor();
        $xslt = $saxon->newXsltProcessor();
        
        $xslt->setSourceFromFile((string) $this->source->toFile());
        $xslt->compileFromFile((string) $this->template->toFile());
        $xslt->setOutputFile((string) $outputFile);
        
        $xslt->transformToFile();
        
        return $outputFile;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toDocument()
     *
     */
    public function writeDocument(): DOMDocument {
        return $this->writeFile()->getDocument();
    }
}

