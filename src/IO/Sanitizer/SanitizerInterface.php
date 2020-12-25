<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;

interface SanitizerInterface {

    public function apply($value);

    public function getDefault();
}

