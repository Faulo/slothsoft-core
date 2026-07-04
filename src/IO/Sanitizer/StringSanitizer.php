<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class StringSanitizer implements SanitizerInterface {
    
    private string $default;
    
    public function __construct(string $default = '') {
        $this->default = $default;
    }
    
    public function apply($value): string {
        $value = (string) $value;
        return $value === '' ? $this->getDefault() : $value;
    }
    
    public function getDefault(): string {
        return $this->default;
    }
}
