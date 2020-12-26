<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see ChunkReaderInterface
 *
 * @todo auto-generated
 */
class ChunkReaderInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(ChunkReaderInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Readable\ChunkReaderInterface'!");
    }
}