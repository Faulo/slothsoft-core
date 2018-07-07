<?php
namespace Slothsoft\Core\IO\Sanitizer;

interface SanitizerInterface
{
    public function apply($value);
    
    public function getDefault();
}

