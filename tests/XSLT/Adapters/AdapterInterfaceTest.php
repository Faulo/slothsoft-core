<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use PHPUnit\Framework\TestCase;

/**
 * AdapterInterfaceTest
 *
 * @see AdapterInterface
 *
 * @todo auto-generated
 */
class AdapterInterfaceTest extends TestCase {
    
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(AdapterInterface::class), "Failed to load interface 'Slothsoft\Core\XSLT\Adapters\AdapterInterface'!");
    }
}