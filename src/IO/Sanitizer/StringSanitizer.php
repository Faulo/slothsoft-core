<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;

class StringSanitizer implements SanitizerInterface {
    
    private $default;
    
    public function __construct(string $default = '') {
        $this->default = $default;
    }
    
    public function apply($value) {
        $value = (string) $value;
        return $value === '' ? $this->getDefault() : $value;
    }
    
    public function getDefault() {
        return $this->default;
    }
}

