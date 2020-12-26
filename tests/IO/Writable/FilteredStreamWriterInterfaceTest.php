<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable;

use PHPUnit\Framework\TestCase;

/**
 * FilteredStreamWriterInterfaceTest
 *
 * @see FilteredStreamWriterInterface
 *
 * @todo auto-generated
 */
class FilteredStreamWriterInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(FilteredStreamWriterInterface::class), "Failed to load interface 'Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface'!");
    }
}