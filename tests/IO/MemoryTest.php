<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO;

use PHPUnit\Framework\TestCase;

/**
 * MemoryTest
 *
 * @see Memory
 *
 * @todo auto-generated
 */
final class MemoryTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Memory::class), "Failed to load class 'Slothsoft\Core\IO\Memory'!");
    }
}