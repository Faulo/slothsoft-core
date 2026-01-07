<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunksDelegate;
use BadMethodCallException;
use Generator;

/**
 * PersistentGeneratorStreamTest
 *
 * @see PersistentGeneratorStream
 */
final class PersistentGeneratorStreamTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(PersistentGeneratorStream::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\PersistentGeneratorStream'!");
    }
    
    private function createSuT(array $values): PersistentGeneratorStream {
        $writer = new ChunkWriterFromChunksDelegate(function () use ($values): Generator {
            yield from $values;
        });
        
        return new PersistentGeneratorStream($writer);
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
    public function test_read(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_is_whole(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_is_remaining_part(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        $sut->read(2);
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual(substr($expected, 2)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_seeks(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        $sut->getContents();
        
        $actual = $sut->tell();
        
        $this->assertThat($actual, new IsEqual(strlen($expected)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_twice(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $sut->getContents();
        
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual(''));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_thrice(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $sut->getContents();
        $sut->rewind();
        $sut->getContents();
        
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual(''));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_toString_is_whole(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        $actual = (string) $sut;
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_toString_is_always_whole(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        $sut->read(2);
        $actual = (string) $sut;
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_toString_seeks(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        (string) $sut;
        
        $actual = $sut->tell();
        
        $this->assertThat($actual, new IsEqual(strlen($expected)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_seek(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $sut->seek(2);
        
        $expected = implode('', $values);
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual(substr($expected, 2)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_eof(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $sut->read(PHP_INT_MAX);
        
        $actual = $sut->eof();
        
        $this->assertThat($actual, new IsEqual(true));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_rewind(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = $sut->read(PHP_INT_MAX);
        $sut->rewind();
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_can_rewind_iff_read_first(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = $sut->read(PHP_INT_MAX);
        $sut->close();
        $sut->rewind();
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_can_seek_iff_read_first(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = $sut->read(PHP_INT_MAX);
        $sut->close();
        $sut->seek(2);
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual(substr($expected, 2)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_reading_after_closing_throws(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $sut->close();
        
        $this->expectException(BadMethodCallException::class);
        $sut->read(PHP_INT_MAX);
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_reading_after_getSize_works(string ...$values): void {
        $sut = $this->createSuT($values);
        
        $expected = implode('', $values);
        
        $sut->getSize();
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual($expected));
    }
}