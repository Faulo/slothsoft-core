<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsIdentical;
use Generator;

/**
 * ChunkWriterFromChunksDelegateTest
 *
 * @see ChunkWriterFromChunksDelegate
 */
final class ChunkWriterFromChunksDelegateTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(ChunkWriterFromChunksDelegate::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Delegates\ChunkWriterFromChunksDelegate'!");
    }
    
    private static array $values;
    
    private static $delegate;
    
    public static function setUpBeforeClass(): void {
        self::$values = [];
        foreach (range(0, 1_000) as $i) {
            self::$values[] = (string) ($i * $i);
        }
        
        self::$delegate = function (): Generator {
            yield from self::$values;
        };
    }
    
    /**
     *
     * @testWith [1]
     *           [2]
     *           [3]
     */
    public function test_values(int $count) {
        $sut = new ChunkWriterFromChunksDelegate(self::$delegate);
        
        $expected = self::$values;
        for ($i = 0; $i < $count; $i ++) {
            $actual = [
                ...$sut->toChunks()
            ];
        }
        
        $this->assertThat($actual, new IsIdentical($expected));
    }
}