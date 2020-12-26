<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see DOMWriterInterface
 *
 * @todo auto-generated
 */
class DOMWriterInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(DOMWriterInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Writable\DOMWriterInterface'!");
    }
}