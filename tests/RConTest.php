<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * RConTest
 *
 * @see RCon
 */
final class RConTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(RCon::class), "Failed to load class 'Slothsoft\Core\RCon'!");
    }
    
    public function testPacketFieldsConsumeTheirInput(): void {
        $sut = new class() extends RCon {
            /** @noinspection PhpMissingParentConstructorInspection */
            public function __construct() {
            }
            
            public function readLong(string &$buffer): int {
                return $this->getLong($buffer);
            }
            
            public function readString(string &$buffer): string {
                return $this->getString($buffer);
            }
        };
        
        $buffer = pack('V', 42) . "hello\0world\0";
        $this->assertSame(42, $sut->readLong($buffer));
        $this->assertSame('hello', $sut->readString($buffer));
        $this->assertSame('world', $sut->readString($buffer));
        $this->assertSame('', $buffer);
        $this->assertSame(0, $sut->readLong($buffer));
        $this->assertSame('', $buffer);
        $this->assertSame('', $sut->readString($buffer));
    }
    
}