<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use PHPUnit\Framework\TestCase;

/**
 * ChunkReaderInterfaceTest
 *
 * @see ChunkReaderInterface
 *
 * @todo auto-generated
 */
final class ChunkReaderInterfaceTest extends TestCase {
    
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(ChunkReaderInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Readable\ChunkReaderInterface'!");
    }
}