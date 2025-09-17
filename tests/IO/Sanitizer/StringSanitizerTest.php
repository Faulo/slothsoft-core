<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;

use PHPUnit\Framework\TestCase;

/**
 * StringSanitizerTest
 *
 * @see StringSanitizer
 *
 * @todo auto-generated
 */
class StringSanitizerTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(StringSanitizer::class), "Failed to load class 'Slothsoft\Core\IO\Sanitizer\StringSanitizer'!");
    }
}