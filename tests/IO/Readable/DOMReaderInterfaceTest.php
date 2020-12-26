<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see DOMReaderInterface
 *
 * @todo auto-generated
 */
class DOMReaderInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(DOMReaderInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Readable\DOMReaderInterface'!");
    }
}