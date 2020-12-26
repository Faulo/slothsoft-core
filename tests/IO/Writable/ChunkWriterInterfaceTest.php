<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use PHPUnit\Framework\TestCase;

/**
 * ChunkWriterInterfaceTest
 *
 * @see ChunkWriterInterface
 *
 * @todo auto-generated
 */
class ChunkWriterInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(ChunkWriterInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Writable\ChunkWriterInterface'!");
    }
}