<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see FileNameSanitizer
 *
 * @todo auto-generated
 */
class FileNameSanitizerTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(FileNameSanitizer::class), "Failed to load class 'Slothsoft\Core\IO\Sanitizer\FileNameSanitizer'!");
    }
}