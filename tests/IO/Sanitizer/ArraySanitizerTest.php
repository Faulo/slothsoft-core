<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;

use PHPUnit\Framework\TestCase;

class ArraySanitizerTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ArraySanitizer::class));
    }
}