<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use PHPUnit\Framework\TestCase;

/**
 * StreamWrapperRegistrarTest
 *
 * @see StreamWrapperRegistrar
 *
 * @todo auto-generated
 */
final class StreamWrapperRegistrarTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(StreamWrapperRegistrar::class), "Failed to load class 'Slothsoft\Core\StreamWrapper\StreamWrapperRegistrar'!");
    }
}