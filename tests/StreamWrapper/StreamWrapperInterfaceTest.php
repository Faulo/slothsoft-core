<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

use PHPUnit\Framework\TestCase;

/**
 * StreamWrapperInterfaceTest
 *
 * @see StreamWrapperInterface
 *
 * @todo auto-generated
 */
class StreamWrapperInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(StreamWrapperInterface::class), "Failed to load interface 'Slothsoft\Core\StreamWrapper\StreamWrapperInterface'!");
    }
}