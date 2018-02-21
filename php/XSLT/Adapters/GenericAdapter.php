<?php
namespace Slothsoft\Core\XSLT\Adapters;

use Slothsoft\Core\XSLT\Inputs\InputInterface;

/**
 *
 * @author Daniel Schulz
 *        
 */
abstract class GenericAdapter implements AdapterInterface
{

    protected $source;

    protected $template;

    protected $param;

    public function setParameters(array $param)
    {
        $this->param = $param;
    }

    public function setSource(InputInterface $input)
    {
        $this->source = $input;
    }

    public function setTemplate(InputInterface $input)
    {
        $this->template = $input;
    }
}

