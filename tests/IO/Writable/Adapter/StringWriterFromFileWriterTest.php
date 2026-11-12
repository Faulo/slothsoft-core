<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Adapter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;
use SplFileObject;

/**
 * StringWriterFromFileWriterTest
 *
 * @see StringWriterFromFileWriter
 */
final class StringWriterFromFileWriterTest extends TestCase {
    
    private function createWriter(SplFileInfo $file): StringWriterFromFileWriter {
        $source = $this->createStub(FileWriterInterface::class);
        $source->method('toFile')->willReturn($file);
        return new StringWriterFromFileWriter($source);
    }
    
    /**
     * @dataProvider contentsProvider
     */
    public function testToString(string $contents): void {
        $path = tempnam(sys_get_temp_dir(), 'file-writer-');
        self::assertNotFalse($path);
        try {
            self::assertSame(strlen($contents), file_put_contents($path, $contents));
            self::assertSame($contents, $this->createWriter(new SplFileInfo($path))->toString());
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
    
    public function testToStringReadsPathOfFileObject(): void {
        $file = new SplFileObject(__FILE__);
        self::assertSame($this->createWriter(new SplFileInfo(__FILE__))->toString(), $this->createWriter($file)->toString());
    }
    
    public function testToStringThrowsForMissingFile(): void {
        $path = __FILE__ . '/missing';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($path);
        $this->createWriter(new SplFileInfo($path))->toString();
    }
    
    public function testToStringThrowsForDirectory(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__DIR__);
        $this->createWriter(new SplFileInfo(__DIR__))->toString();
    }
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(StringWriterFromFileWriter::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Adapter\StringWriterFromFileWriter'!");
    }
}
