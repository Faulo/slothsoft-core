<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\Assert\assertThat as substr;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsNull;
use Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunksDelegate;
use BadMethodCallException;
use Generator;

/**
 * OneTimeGeneratorStreamTest
 *
 * @see OneTimeGeneratorStream
 */
final class OneTimeGeneratorStreamTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(OneTimeGeneratorStream::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\OneTimeGeneratorStream'!");
    }
    
    private function createSuT(array $values): OneTimeGeneratorStream {
        $writer = new ChunkWriterFromChunksDelegate(function () use ($values): Generator {
            yield from $values;
        });
        
        return new OneTimeGeneratorStream($writer);
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
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getSize(string ...$values) {
        $sut = $this->createSuT($values);
        
        $actual = $sut->getSize();
        
        $this->assertThat($actual, new IsNull());
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_first(string ...$values) {
        $sut = $this->createSuT($values);
        
        $expected = $values[0];
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_second(string ...$values) {
        $sut = $this->createSuT($values);
        
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
        $sut = $this->createSuT($values);
        
        $expected = substr($values[0], 0, 2);
        $actual = $sut->read(2);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read_part_of_first_twice(string ...$values) {
        $sut = $this->createSuT($values);
        
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
        $sut = $this->createSuT($values);
        
        $this->expectException(BadMethodCallException::class);
        $sut->seek(2);
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_eof(string ...$values) {
        $sut = $this->createSuT($values);
        
        $sut->read(PHP_INT_MAX);
        
        $actual = $sut->eof();
        
        $this->assertThat($actual, new IsEqual(false));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_rewind(string ...$values) {
        $sut = $this->createSuT($values);
        
        $this->expectException(BadMethodCallException::class);
        $sut->rewind();
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_reading_after_closing_throws(string ...$values) {
        $sut = $this->createSuT($values);
        
        $sut->close();
        
        $this->expectException(BadMethodCallException::class);
        $sut->read(PHP_INT_MAX);
    }
}