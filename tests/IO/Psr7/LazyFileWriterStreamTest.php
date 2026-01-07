<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\Delegates\FileWriterFromFileDelegate;
use BadMethodCallException;
use SplFileInfo;

/**
 * LazyFileWriterStreamTest
 *
 * @see LazyFileWriterStream
 */
final class LazyFileWriterStreamTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(LazyFileWriterStream::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\LazyFileWriterStream'!");
    }
    
    private function createSuT(string $expected): LazyFileWriterStream {
        $file = FileInfoFactory::createFromString($expected);
        $writer = new FileWriterFromFileDelegate(function () use ($file): SplFileInfo {
            return $file;
        });
        
        return new LazyFileWriterStream($writer);
    }
    
    public function valuesProvider(): iterable {
        yield '1-2-3' => [
            'one-two-three'
        ];
        
        yield 'hello world' => [
            'hello-world'
        ];
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_read(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_is_whole(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_is_remaining_part(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->read(2);
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual(substr($expected, 2)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_seeks(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->getContents();
        
        $actual = $sut->tell();
        
        $this->assertThat($actual, new IsEqual(strlen($expected)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_twice(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->getContents();
        
        $actual = $sut->getContents();
        
        $this->assertThat($actual, new IsEqual(''));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_getContents_thrice(string $expected): void {
        $sut = $this->createSuT($expected);
        
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
    public function test_toString_is_whole(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $actual = (string) $sut;
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_toString_is_always_whole(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->read(2);
        $actual = (string) $sut;
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_toString_seeks(string $expected): void {
        $sut = $this->createSuT($expected);
        
        (string) $sut;
        
        $actual = $sut->tell();
        
        $this->assertThat($actual, new IsEqual(strlen($expected)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_seek(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->seek(2);
        
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual(substr($expected, 2)));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_eof(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->read(PHP_INT_MAX);
        
        $actual = $sut->eof();
        
        $this->assertThat($actual, new IsEqual(true));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_rewind(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $expected = $sut->read(PHP_INT_MAX);
        $sut->rewind();
        $actual = $sut->read(PHP_INT_MAX);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    /**
     *
     * @dataProvider valuesProvider
     */
    public function test_that_can_rewind_iff_read_first(string $expected): void {
        $sut = $this->createSuT($expected);
        
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
    public function test_that_can_seek_iff_read_first(string $expected): void {
        $sut = $this->createSuT($expected);
        
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
    public function test_that_reading_after_closing_throws(string $expected): void {
        $sut = $this->createSuT($expected);
        
        $sut->close();
        
        $this->expectException(BadMethodCallException::class);
        $sut->read(PHP_INT_MAX);
    }
}