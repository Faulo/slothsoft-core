<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class StringSanitizer implements SanitizerInterface {
    
    private string $default;
    
    /**
     * @param string $default
     */
    public function __construct(string $default = '') {
        $this->default = $default;
    }
    
    /**
     * @param mixed $value
     * @return string
     */
    public function apply($value): string {
        $value = (string) $value;
        return $value === '' ? $this->getDefault() : $value;
    }
    
    /**
     * @return string
     */
    public function getDefault(): string {
        return $this->default;
    }
}
