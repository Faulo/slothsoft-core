<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

use Slothsoft\Core\FileSystem;

final class FileNameSanitizer implements SanitizerInterface {
    
    private string $default;
    
    /**
     * @param string $default
     * @return void
     */
    public function __construct(string $default = '') {
        $this->default = $default;
    }
    
    /**
     * @param mixed $value
     * @return string
     */
    public function apply($value): string {
        $value = FileSystem::filenameSanitize((string) $value);
        return $value === '' ? $this->getDefault() : $value;
    }
    
    /**
     * @return string
     */
    public function getDefault(): string {
        return $this->default;
    }
}
