<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use DOMDocument;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\XSLT\Inputs\InputInterface;

/**
 * Shared state and DOM output behavior for XSLT processor adapters.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
abstract class GenericAdapter implements AdapterInterface {
    
    protected InputInterface $source;
    
    protected InputInterface $template;
    
    protected array $param = [];
    
    /**
     * @param array $param
     * @return void
     */
    public function setParameters(array $param) {
        $this->param = $param;
    }
    
    /**
     * @param InputInterface $input
     * @return void
     */
    public function setSource(InputInterface $input) {
        $this->source = $input;
    }
    
    /**
     * @param InputInterface $input
     * @return void
     */
    public function setTemplate(InputInterface $input) {
        $this->template = $input;
    }
    
    /**
     * @return DOMDocument
     */
    public function writeDocument(): DOMDocument {
        return DOMHelper::loadDocument((string) $this->writeFile());
    }
}
