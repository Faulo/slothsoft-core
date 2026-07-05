<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use DOMDocument;
use Slothsoft\Core\XSLT\Inputs\InputInterface;
use SplFileInfo;

/**
 * XSLT processor adapter that transforms source and template inputs into files or DOM documents.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
interface AdapterInterface {
    
    /**
     * @param InputInterface $input
     * @return void
     */
    public function setSource(InputInterface $input);
    
    /**
     * @param InputInterface $input
     * @return void
     */
    public function setTemplate(InputInterface $input);
    
    /**
     * @param array $param
     * @return void
     */
    public function setParameters(array $param);
    
    /**
     * @param SplFileInfo|null $outputFile
     * @return SplFileInfo
     */
    public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo;
    
    /**
     * @return DOMDocument
     */
    public function writeDocument(): DOMDocument;
}
