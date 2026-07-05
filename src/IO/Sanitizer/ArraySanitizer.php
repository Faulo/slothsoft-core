<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class ArraySanitizer implements SanitizerInterface {
    
    private array $default;
    
    /**
     * @param array $default
     * @return void
     */
    public function __construct(array $default = []) {
        $this->default = $default;
    }
    
    /**
     * @param mixed $value
     * @return array
     */
    public function apply($value): array {
        return is_array($value) ? $value : $this->getDefault();
    }
    
    /**
     * @return array
     */
    public function getDefault(): array {
        return $this->default;
    }
}
