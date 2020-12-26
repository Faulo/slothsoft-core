<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use PHPUnit\Framework\TestCase;

/**
 * StreamWriterInterfaceTest
 *
 * @see StreamWriterInterface
 *
 * @todo auto-generated
 */
class StreamWriterInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(StreamWriterInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Writable\StreamWriterInterface'!");
    }
}