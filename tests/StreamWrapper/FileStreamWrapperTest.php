<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use PHPUnit\Framework\TestCase;

/**
 * FileStreamWrapperTest
 *
 * @see FileStreamWrapper
 *
 */
final class FileStreamWrapperTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(FileStreamWrapper::class), "Failed to load class 'Slothsoft\Core\StreamWrapper\FileStreamWrapper'!");
    }
    
    public function testMissingFileFailsBeforeCreatingAResourceWrapper(): void {
        $this->expectException(\RuntimeException::class);
        new FileStreamWrapper(new \SplFileInfo(__DIR__ . '/missing-' . uniqid()));
    }
}
