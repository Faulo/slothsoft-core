<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use Generator;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;

/**
 * StringWriterFromChunkWriterTest
 *
 * @see StringWriterFromChunkWriter
 */
final class StringWriterFromChunkWriterTest extends TestCase implements ChunkWriterInterface {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(StringWriterFromChunkWriter::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Adapter\StringWriterFromChunkWriter'!");
    }
    
    private static array $values;
    
    private static string $expected;
    
    public static function setUpBeforeClass(): void {
        self::$values = [];
        foreach (range(0, 10_000) as $i) {
            self::$values[] = (string) ($i * $i);
        }
        self::$expected = implode(self::$values);
    }
    
    public function toChunks(): Generator {
        yield from self::$values;
    }
    
    private const ITERATIONS = 10_000;
    
    public function test_string() {
        $sut = new StringWriterFromChunkWriter($this);
        
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $actual = $sut->toString();
            $this->assertThat($actual, new IsIdentical(self::$expected));
        }
    }
}