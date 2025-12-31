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
    
    /**
     *
     * @testWith [1]
     *           [2]
     *           [3]
     */
    public function test_values(int $count) {
        $sut = new ChunkWriterMemoryCache($this);
        
        $expected = self::$values;
        for ($i = 0; $i < $count; $i ++) {
            $actual = [
                ...$sut->toChunks()
            ];
        }
        
        $this->assertThat($actual, new IsIdentical($expected));
    }
}