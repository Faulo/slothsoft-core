<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\LogicalNot;
use Slothsoft\Core\IO\Writable\StringWriterInterface;

/**
 * DOMWriterFromStringWriterTest
 *
 * @see DOMWriterFromStringWriter
 */
final class DOMWriterFromStringWriterTest extends TestCase implements StringWriterInterface {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DOMWriterFromStringWriter::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Adapter\DOMWriterFromStringWriter'!");
    }
    
    public function toString(): string {
        return '<xml/>';
    }
    
    public function test_loadString() {
        $sut = new DOMWriterFromStringWriter($this);
        
        $actual = $sut->toDocument();
        
        $this->assertThat($actual->documentElement->tagName, new IsEqual('xml'));
    }
    
    public function test_toDocument_doesNotCache() {
        $sut = new DOMWriterFromStringWriter($this);
        
        $expected = $sut->toDocument();
        $actual = $sut->toDocument();
        
        $this->assertThat($actual, new LogicalNot(new IsIdentical($expected)));
    }
}