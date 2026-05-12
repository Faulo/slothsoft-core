<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamWrapper;

use PHPUnit\Framework\TestCase;

/**
 * FileStreamWrapperTest
 *
 * @see FileStreamWrapper
 *
 * @todo auto-generated
 */
final class FileStreamWrapperTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(FileStreamWrapper::class), "Failed to load class 'Slothsoft\Core\StreamWrapper\FileStreamWrapper'!");
    }
}