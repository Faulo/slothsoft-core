<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use PHPUnit\Framework\TestCase;

/**
 * StringWriterInterfaceTest
 *
 * @see StringWriterInterface
 *
 * @todo auto-generated
 */
final class StringWriterInterfaceTest extends TestCase {
    
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(StringWriterInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Writable\StringWriterInterface'!");
    }
}