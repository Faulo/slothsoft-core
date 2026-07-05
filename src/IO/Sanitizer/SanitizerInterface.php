<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

interface SanitizerInterface {
    
    /**
     * @param mixed $value
     * @return void
     */
    public function apply($value);
    
    /**
     * @return void
     */
    public function getDefault();
}

