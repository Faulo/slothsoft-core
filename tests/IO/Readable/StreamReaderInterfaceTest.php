<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Readable;

use PHPUnit\Framework\TestCase;

/**
 * StreamReaderInterfaceTest
 *
 * @see StreamReaderInterface
 *
 * @todo auto-generated
 */
class StreamReaderInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(StreamReaderInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Readable\StreamReaderInterface'!");
    }
}