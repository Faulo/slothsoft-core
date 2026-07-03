<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

class ArraySanitizer implements SanitizerInterface {
    
    private array $default;
    
    public function __construct(array $default = []) {
        $this->default = $default;
    }
    
    public function apply($value) {
        return is_array($value) ? $value : $this->getDefault();
    }
    
    public function getDefault() {
        return $this->default;
    }
}
