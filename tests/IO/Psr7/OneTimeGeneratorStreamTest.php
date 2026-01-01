<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsNull;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use BadMethodCallException;
use Generator;

/**
 * OneTimeGeneratorStreamTest
 *
 * @see OneTimeGeneratorStream
 */
final class OneTimeGeneratorStreamTest extends TestCase implements ChunkWriterInterface {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(OneTimeGeneratorStream::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\OneTimeGeneratorStream'!");
    }
    
    private array $values;
    
    public function toChunks(): Generator {
        yield from $this->values;
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getSize(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $actual = $sut->getSize();
        
        $this->assertThat($actual, new IsNull());
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_first(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $expected = $values[0];
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_second(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $expected = $values[1];
        $actual = $sut->read(PHP_INT_MAX);
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_part_of_first(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $expected = substr($values[0], 0, 2);
        $actual = $sut->read(2);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_part_of_first_twice(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $expected = [
            substr($values[0], 0, 2),
            substr($values[0], 2, 100),
            $values[1]
        ];
        
        $actual = [];
        $actual[] = $sut->read(2);
        $actual[] = $sut->read(100);
        $actual[] = $sut->read(100);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_seek(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $this->expectException(BadMethodCallException::class);
        $sut->seek(2);
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_eof(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $sut->read(PHP_INT_MAX);
        
        $actual = $sut->eof();
        
        $this->assertThat($actual, new IsEqual(false));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_rewind(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $this->expectException(BadMethodCallException::class);
        $sut->rewind();
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_reading_after_closing_throws(string ...$values) {
        $this->values = $values;
        
        $sut = new OneTimeGeneratorStream($this);
        
        $sut->close();
        
        $this->expectException(BadMethodCallException::class);
        $sut->read(PHP_INT_MAX);
    }
    
    public function valuesProvider(): iterable {
        yield '1-2-3' => [
            'one',
            'two',
            'three'
        ];
        
        yield 'hello world' => [
            'hello',
            'world'
        ];
    }
}