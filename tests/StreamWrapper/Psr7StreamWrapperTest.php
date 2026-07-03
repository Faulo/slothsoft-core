<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

/**
 * Psr7StreamWrapperTest
 *
 * @see Psr7StreamWrapper
 */
final class Psr7StreamWrapperTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Psr7StreamWrapper::class), "Failed to load class 'Slothsoft\Core\StreamWrapper\Psr7StreamWrapper'!");
    }
    
    /**
     *
     * @test
     */
    public function streamTellReturnsCurrentStreamPosition(): void {
        $stream = Utils::streamFor('abc');
        $sut = new Psr7StreamWrapper($stream);
        
        $this->assertSame('a', $sut->stream_read(1));
        $this->assertSame(1, $sut->stream_tell());
    }
}
