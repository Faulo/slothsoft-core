<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Sanitizer;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @todo auto-generated
 */
class SanitizerInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(SanitizerInterface::class));
    }
}