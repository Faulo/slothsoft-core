<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;

use Slothsoft\Core\FileSystem;

class FileNameSanitizer implements SanitizerInterface {

    private $default;

    public function __construct(string $default = '') {
        $this->default = $default;
    }

    public function apply($value) {
        $value = FileSystem::filenameSanitize((string) $value);
        return $value === '' ? $this->getDefault() : $value;
    }

    public function getDefault() {
        return $this->default;
    }
}

