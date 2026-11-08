<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;

/**
 * AbstractFilteredStreamTest
 *
 * @see AbstractFilteredStream
 *
 */
final class AbstractFilteredStreamTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AbstractFilteredStream::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\AbstractFilteredStream'!");
    }
    
    public function testStringConversionRewindsAfterPartialRead(): void {
        $stream = new ChunkedFilteredStream(\GuzzleHttp\Psr7\Utils::streamFor('hello'));
        $stream->read(1);
        $stream->read(1);
        $expected = "5\r\nhello\r\n0\r\n\r\n";
        for ($i = 0; $i < 2; $i++) {
            $this->assertSame($expected, (string) $stream);
        }
    }
}
