<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class TokenSanitizer implements SanitizerInterface {
    
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
        if (is_string($value)) {
            $result = [];
            foreach ((preg_split('~\s+~', $value) ?: []) as $val) {
                $val = trim($val);
                if (strlen($val)) {
                    $result[] = $val;
                }
            }
            return $result;
        }
        if (is_array($value)) {
            return $value;
        }
        return $this->getDefault();
    }
    
    /**
     * @return array<mixed>
     */
    public function getDefault(): array {
        return $this->default;
    }
}
