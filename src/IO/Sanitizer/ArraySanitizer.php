<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class ArraySanitizer implements SanitizerInterface {
    
    /** @var array<mixed> */
    private array $default;
    
    /**
     * @param array<mixed> $default
     */
    public function __construct(array $default = []) {
        $this->default = $default;
    }
    
    /**
     * @param mixed $value
     * @return array<mixed>
     */
    public function apply($value): array {
        return is_array($value) ? $value : $this->getDefault();
    }
    
    /**
     * @return array<mixed>
     */
    public function getDefault(): array {
        return $this->default;
    }
}
