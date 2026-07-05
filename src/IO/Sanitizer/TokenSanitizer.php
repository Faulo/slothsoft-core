<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

final class TokenSanitizer implements SanitizerInterface {
    
    private array $default;
    
    /**
     * @param array $default
     */
    public function __construct(array $default = []) {
        $this->default = $default;
    }
    
    /**
     * @param mixed $value
     * @return array
     */
    public function apply($value): array {
        if (is_string($value)) {
            $result = [];
            foreach (preg_split('~\s+~', $value) as $val) {
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
     * @return array
     */
    public function getDefault(): array {
        return $this->default;
    }
}
