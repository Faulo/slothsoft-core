<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

use PHPUnit\Framework\TestCase;

/**
 * StreamFilterInterfaceTest
 *
 * @see StreamFilterInterface
 *
 * @todo auto-generated
 */
class StreamFilterInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(StreamFilterInterface::class), "Failed to load interface 'Slothsoft\Core\StreamFilter\StreamFilterInterface'!");
    }
}