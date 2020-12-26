<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @todo auto-generated
 */
class InputInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(InputInterface::class), "Failed to load interface 'Slothsoft\Core\XSLT\Inputs\InputInterface'!");
    }
}