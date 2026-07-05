<?php
/** @noinspection PhpUndefinedNamespaceInspection,PhpUndefinedClassInspection */
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use Saxon\SaxonProcessor;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

/**
 * XSLT adapter backed by the Saxon/C PHP extension.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
final class SaxonProcessorAdapter extends GenericAdapter {
    
    private function newSaxonProcessor(): SaxonProcessor {
        return new SaxonProcessor();
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()
     *
     * @param SplFileInfo|null $outputFile
     * @return SplFileInfo
     */
    public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo {
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
    
}
