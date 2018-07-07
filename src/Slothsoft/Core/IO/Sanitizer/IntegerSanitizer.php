<?php
namespace Slothsoft\Core\IO\Sanitizer;

class IntegerSanitizer implements SanitizerInterface 
{
    private $default;
    public function __construct(int $default = 0) {
        $this->default = $default;
    }
    
    public function apply($value)
    {
        $value = filter_var((string) $value, FILTER_SANITIZE_NUMBER_INT);
        return $value === '' ? $this->getDefault() : (int) $value;
    }

    public function getDefault()
    {
        return $this->default;
    }
}

