<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * FileInfoTest
 *
 * @see FileInfo
 */
final class FileInfoTest extends TestCase {
    
    /**
     * @dataProvider contentsProvider
     */
    public function testReadContents(string $contents): void {
        $path = tempnam(sys_get_temp_dir(), 'file-info-');
        self::assertNotFalse($path);
        try {
            self::assertSame(strlen($contents), file_put_contents($path, $contents));
            self::assertSame($contents, FileInfo::readContents($path));
            self::assertSame($contents, (new FileInfo($path))->toString());
        } finally {
            unlink($path);
        }
    }
    
    /**
     * @return array<string, array{string}>
     */
    public function contentsProvider(): array {
        return [
            'text' => ["Hello, world!\n"],
            'empty' => [''],
            'binary' => ["\x00\xff\r\n\x80"]
        ];
    }
    
    public function testReadContentsThrowsForMissingFile(): void {
        $path = __FILE__ . '/missing';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($path);
        FileInfo::readContents($path);
    }
    
    public function testToStringThrowsForMissingFile(): void {
        $path = __FILE__ . '/missing';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($path);
        (new FileInfo($path))->toString();
    }
    
    public function testReadContentsThrowsForDirectory(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__DIR__);
        FileInfo::readContents(__DIR__);
    }
    
    public function testToStringThrowsForDirectory(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__DIR__);
        (new FileInfo(__DIR__))->toString();
    }
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(FileInfo::class), "Failed to load class 'Slothsoft\Core\IO\FileInfo'!");
    }
}
