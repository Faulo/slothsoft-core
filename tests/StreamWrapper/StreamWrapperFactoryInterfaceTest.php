<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;
        
use PHPUnit\Framework\TestCase;
        
/**
 * StreamWrapperFactoryInterfaceTest
 *
 * @see StreamWrapperFactoryInterface
 *
 * @todo auto-generated
 */
class StreamWrapperFactoryInterfaceTest extends TestCase {
        
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(StreamWrapperFactoryInterface::class), "Failed to load interface 'Slothsoft\Core\StreamWrapper\StreamWrapperFactoryInterface'!");
    }
}