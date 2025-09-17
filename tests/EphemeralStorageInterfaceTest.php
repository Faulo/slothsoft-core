<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * EphemeralStorageInterfaceTest
 *
 * @see EphemeralStorageInterface
 *
 * @todo auto-generated
 */
class EphemeralStorageInterfaceTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(EphemeralStorageInterface::class), "Failed to load interface 'Slothsoft\Core\EphemeralStorageInterface'!");
    }
}