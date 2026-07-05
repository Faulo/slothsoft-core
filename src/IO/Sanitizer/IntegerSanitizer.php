<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class IntegerSanitizer implements SanitizerInterface {
    
    private int $default;
    
    /**
     * @param int $default
     */
    public function __construct(int $default = 0) {
        $this->default = $default;
    }
    
    /**
     * @param mixed $value
     * @return int
     */
    public function apply($value): int {
        $value = filter_var((string) $value, FILTER_SANITIZE_NUMBER_INT);
        return $value === '' ? $this->getDefault() : (int) $value;
    }
    
    /**
     * @return int
     */
    public function getDefault(): int {
        return $this->default;
    }
}
