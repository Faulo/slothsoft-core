<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @todo auto-generated
 */
class FileWriterInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(FileWriterInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Writable\FileWriterInterface'!");
    }
}