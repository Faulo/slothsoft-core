<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * HTTPFileTest
 *
 * @see HTTPFile
 */
final class HTTPFileTest extends TestCase {
    
    /**
     * @dataProvider contentsProvider
     */
    public function testGetContents(string $contents): void {
        $path = tempnam(sys_get_temp_dir(), 'http-file-');
        self::assertNotFalse($path);
        try {
            self::assertSame(strlen($contents), file_put_contents($path, $contents));
            /** @noinspection PhpDeprecationInspection */
            $file = HTTPFile::createFromPath($path);
            self::assertSame($contents, $file->getContents());
            self::assertSame($contents, $file->toString());
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
    
    public function testGetContentsThrowsForMissingFile(): void {
        $path = __FILE__ . '/missing';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($path);
        /** @noinspection PhpDeprecationInspection */
        HTTPFile::createFromPath($path)->getContents();
    }
    
    public function testGetContentsThrowsForDirectory(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__DIR__);
        /** @noinspection PhpDeprecationInspection */
        HTTPFile::createFromPath(__DIR__)->getContents();
    }
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        /** @noinspection PhpDeprecationInspection */
        $this->assertTrue(class_exists(HTTPFile::class), "Failed to load class 'Slothsoft\Core\IO\HTTPFile'!");
    }
}
