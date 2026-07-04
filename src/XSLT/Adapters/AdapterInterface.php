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
    
    public function setSource(InputInterface $input);
    
    public function setTemplate(InputInterface $input);
    
    public function setParameters(array $param);
    
    public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo;
    
    public function writeDocument(): DOMDocument;
}
