<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @todo auto-generated
 */
class FileReaderInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(FileReaderInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Readable\FileReaderInterface'!");
    }
}