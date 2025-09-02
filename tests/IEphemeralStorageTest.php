<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * IEphemeralStorageTest
 *
 * @see IEphemeralStorage
 *
 * @todo auto-generated
 */
class IEphemeralStorageTest extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(IEphemeralStorage::class), "Failed to load interface 'Slothsoft\Core\IEphemeralStorage'!");
    }
}