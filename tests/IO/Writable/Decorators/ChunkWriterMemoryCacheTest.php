<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsIdentical;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Generator;

/**
 * ChunkWriterMemoryCacheTest
 *
 * @see ChunkWriterMemoryCache
 */
final class ChunkWriterMemoryCacheTest extends TestCase implements ChunkWriterInterface {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(ChunkWriterMemoryCache::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Decorators\ChunkWriterMemoryCache'!");
    }
    
    private static array $values;
    
    public static function setUpBeforeClass(): void {
        self::$values = [];
        foreach (range(0, 1_000) as $i) {
            self::$values[] = (string) ($i * $i);
        }
    }
    
    public function toChunks(): Generator {
        yield from self::$values;
    }
    
    public function test_values() {
        $sut = new ChunkWriterMemoryCache($this);
        
        $expected = self::$values;
        $actual = [
            ...$sut->toChunks()
        ];
        
        $this->assertThat($actual, new IsIdentical($expected));
    }
    
    public function test_values_twice() {
        $sut = new ChunkWriterMemoryCache($this);
        
        $expected = self::$values;
        $actual = [
            ...$sut->toChunks()
        ];
        $actual = [
            ...$sut->toChunks()
        ];
        
        $this->assertThat($actual, new IsIdentical($expected));
    }
}