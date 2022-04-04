<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use PHPUnit\Framework\TestCase;

/**
 * ChunkWriterFromGeneratorTest
 *
 * @see ChunkWriterFromGenerator
 */
class ChunkWriterFromGeneratorTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ChunkWriterFromGenerator::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Adapter\ChunkWriterFromGenerator'!");
    }

    /**
     *
     * @testWith [1]
     *           [3]
     */
    public function testToChunks(int $size): void {
        $range = function (int $n) {
            for ($i = 0; $i < $n; $i ++) {
                yield $i;
            }
        };

        $generator = $range($size);

        $writer = new ChunkWriterFromGenerator($generator);

        $this->assertEquals($generator, $writer->toChunks());

        $this->assertEquals(iterator_to_array($range($size)), iterator_to_array($writer->toChunks()));
    }
}