<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use SplFileInfo;
use Slothsoft\Core\XSLT\Inputs\InputInterface;
use DOMDocument;

/**
 *
 * @author Daniel Schulz
 *        
 */
interface AdapterInterface {
    
    public function setSource(InputInterface $input);
    
    public function setTemplate(InputInterface $input);
    
    public function setParameters(array $param);
    
    public function writeFile(SplFileInfo $outputFile = null): SplFileInfo;
    
    public function writeDocument(): DOMDocument;
}

