<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

interface SanitizerInterface {
    
    /**
     * @param mixed $value
     * @return mixed
     */
    public function apply($value);
    
    /**
     * @return mixed
     */
    public function getDefault();
}

