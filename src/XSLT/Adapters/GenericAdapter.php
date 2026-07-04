<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use DOMDocument;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\XSLT\Inputs\InputInterface;

/**
 *
 * @author Daniel Schulz
 *
 */
abstract class GenericAdapter implements AdapterInterface {
    
    protected InputInterface $source;
    
    protected InputInterface $template;
    
    protected array $param = [];
    
    public function setParameters(array $param) {
        $this->param = $param;
    }
    
    public function setSource(InputInterface $input) {
        $this->source = $input;
    }
    
    public function setTemplate(InputInterface $input) {
        $this->template = $input;
    }
    
    public function writeDocument(): DOMDocument {
        return DOMHelper::loadDocument((string) $this->writeFile());
    }
}
