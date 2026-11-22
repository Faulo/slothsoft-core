<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use DOMDocument;
use DOMElement;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class FileSystemTest extends TestCase {
    
    public function testAsNode(): void {
        $document = FileSystem::asNode(__DIR__);
        
        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertInstanceOf(DOMElement::class, $document->documentElement);
    }
    
    /**
     * @runInSeparateProcess
     * @dataProvider cachedDirectorySizes
     * @throws Exception
     */
    public function testAsNodeTotalsLargeDirectories(string $childSize, string $expectedSize, string $expectedDisplaySize): void {
        if (PHP_INT_SIZE < 8) {
            $this->markTestSkipped('This directory total requires 64-bit PHP integers.');
        }
        
        $directory = temp_dir(__NAMESPACE__);
        try {
            $childDirectory = $directory . '/child';
            mkdir($childDirectory);
            file_put_contents($directory . '/one.bin', 'x');
            
            $childKey = FileSystem::generateStorageKey($childDirectory);
            $storage = $this->createMock(EphemeralStorageInterface::class);
            $storage->method('retrieveXML')->willReturnCallback(static function (string $key, int $modifyTime, ?DOMDocument $document) use ($childKey, $childSize): ?DOMElement {
                if (explode('#', $key, 2)[0] !== $childKey) {
                    return null;
                }
                $document = $document ?? new DOMDocument();
                $container = $document->createElement('cache');
                $folder = $document->createElement('folder');
                $folder->setAttribute('size-int', $childSize);
                $container->appendChild($folder);
                return $container;
            });
            FileSystem::setStorage($storage);
            
            $document = FileSystem::asNode($directory);
            $this->assertInstanceOf(DOMDocument::class, $document);
            $folder = $document->documentElement;
            $this->assertInstanceOf(DOMElement::class, $folder);
            $this->assertSame($expectedSize, $folder->getAttribute('size-int'));
            $this->assertSame($expectedDisplaySize, $folder->getAttribute('size-string'));
        } finally {
            FileSystem::removeDir($directory);
        }
    }
    
    /** @return iterable<string, array{string, string, string}> */
    public function cachedDirectorySizes(): iterable {
        yield 'exact total above float precision' => ['9007199254740992', '9007199254740993', '8.00 PB'];
    }
    
    public function testSizeOfEmptyAndMissingFile(): void {
        $path = tempnam(sys_get_temp_dir(), 'core-size-');
        $this->assertIsString($path);
        try {
            $this->assertSame(0, FileSystem::size($path));
        } finally {
            unlink($path);
        }
        clearstatcache(true, $path);
        $this->assertNull(FileSystem::size($path));
    }
    
    /** @runInSeparateProcess */
    public function testSizeAndChunkAtOffsetAboveTwoGiB(): void {
        if (PHP_INT_SIZE < 8) {
            $this->markTestSkipped('This file offset requires 64-bit PHP integers.');
        }
        
        $path = tempnam(sys_get_temp_dir(), 'core-sparse-');
        $this->assertIsString($path);
        $offset = 2147483685;
        $payload = "\x00A\xffZ";
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $sparse = new Process(['fsutil.exe', 'sparse', 'setflag', $path]);
                if ($sparse->run() !== 0) {
                    $this->markTestSkipped('The Windows temporary file could not be marked sparse: ' . trim($sparse->getOutput() . $sparse->getErrorOutput()));
                }
            }
            
            $handle = fopen($path, 'r+b');
            $this->assertIsResource($handle);
            try {
                $this->assertSame(0, fseek($handle, $offset));
                $this->assertSame(strlen($payload), fwrite($handle, $payload));
                $this->assertTrue(fflush($handle));
            } finally {
                fclose($handle);
            }
            clearstatcache(true, $path);
            
            $this->assertSame($offset + strlen($payload), FileSystem::size($path));
            $this->expectOutputString("4\n$payload\n0\n            \n");
            FileSystem::outputChunk($path, (string) $offset, 4.0);
        } finally {
            unlink($path);
        }
    }
    
    /** @runInSeparateProcess
     * @throws \Exception
     */
    public function testOutputChunkThrowsBeyondIntMax(): void {
        $path = temp_file(__METHOD__);
        try {
            file_put_contents($path, 'abc');
            $this->expectException(InvalidArgumentException::class);
            FileSystem::outputChunk($path, '100000000000000000000e-20', 10);
        } finally {
            unlink($path);
        }
    }
    
    /**
     * @dataProvider invalidByteCounts
     * @param mixed $value
     */
    public function testOutputChunkRejectsInvalidByteCounts($value): void {
        $this->expectException(InvalidArgumentException::class);
        FileSystem::outputChunk('unused', $value, 1);
    }
    
    /** @return iterable<string, array{mixed}> */
    public function invalidByteCounts(): iterable {
        yield 'negative' => [-1];
        yield 'negative string' => ['-1'];
        yield 'nonnumeric string' => ['garbage'];
        yield 'short fraction' => ['1.5'];
        yield 'fraction' => ['1.0000000000000000001'];
        yield 'overflowing numeric string' => [PHP_INT_MAX . '0'];
        yield 'overflowing float' => [(float) PHP_INT_MAX + 1.0];
        yield 'infinite' => [INF];
    }
    
    /**
     *
     * @dataProvider createSanitizedFilenames
     */
    public function testFilenameSanitize(string $input, string $output): void {
        $this->assertEquals($output, FileSystem::filenameSanitize($input));
    }
    
    /** @return iterable<mixed> */
    public function createSanitizedFilenames(): iterable {
        return [
            [
                'A',
                'A'
            ],
            [
                "A\u{2044}B",
                'AB'
            ],
            [
                '/\\A?: !*B|<>',
                'A - B'
            ],
            [
                'öäü ÖÄÜ',
                'oau OAU'
            ]
        ];
    }
    
    /** @throws Exception */
    public function testScanDirSortPreservesNumericFilenamesAsStrings(): void {
        $directory = temp_dir(__NAMESPACE__);
        try {
            foreach (['123', 'a.b.txt', 'a.z'] as $filename) {
                $this->assertTrue(touch($directory . DIRECTORY_SEPARATOR . $filename));
            }
            
            $this->assertSame(['123', 'a.z', 'a.b.txt'], FileSystem::scanDir($directory, FileSystem::SCANDIR_SORT));
        } finally {
            FileSystem::removeDir($directory);
        }
    }
    
    /** @throws Exception */
    public function testDirModifyTimePreservesNumericDirectoryKeys(): void {
        $directory = temp_dir(__NAMESPACE__);
        try {
            $numericDirectory = $directory . DIRECTORY_SEPARATOR . '123';
            $this->assertTrue(mkdir($numericDirectory . DIRECTORY_SEPARATOR . '456', 0777, true));
            $this->assertTrue(touch($numericDirectory . DIRECTORY_SEPARATOR . 'file.txt'));
            
            $modifyTimes = FileSystem::dirModifyTime($numericDirectory);
            $this->assertIsArray($modifyTimes);
            $this->assertCount(3, $modifyTimes);
            $this->assertArrayHasKey('123' . DIRECTORY_SEPARATOR . 'file.txt', $modifyTimes);
            $keys = array_keys($modifyTimes);
            $this->assertContains(123, $keys);
            $this->assertContains(456, $keys);
        } finally {
            FileSystem::removeDir($directory);
        }
    }
    
    public function testRemoveDirIncludingRoot(): void {
        $directory = temp_dir(__NAMESPACE__);
        
        mkdir("$directory/A", 0777, true);
        file_put_contents("$directory/A/B", 'test');
        
        $this->assertFileExists("$directory/A/B");
        
        FileSystem::removeDir($directory, false);
        
        $this->assertFileDoesNotExist("$directory/A/B");
        $this->assertDirectoryDoesNotExist("$directory/A");
        $this->assertDirectoryDoesNotExist($directory);
    }
    
    public function testRemoveDirExcludingRoot(): void {
        $directory = temp_dir(__NAMESPACE__);
        
        mkdir("$directory/A", 0777, true);
        file_put_contents("$directory/A/B", 'test');
        
        $this->assertFileExists("$directory/A/B");
        
        FileSystem::removeDir($directory, true);
        
        $this->assertFileDoesNotExist("$directory/A/B");
        $this->assertDirectoryDoesNotExist("$directory/A");
        $this->assertDirectoryExists($directory);
    }
    
    /**
     *
     * @dataProvider commandExamples
     */
    public function testCommandExist(string $command, bool $expected): void {
        $this->assertEquals($expected, FileSystem::commandExists($command));
    }
    
    /** @return iterable<mixed> */
    public function commandExamples(): iterable {
        return [
            [
                'php',
                true
            ],
            [
                'phpasdasdasd',
                false
            ]
        ];
    }
    
    public function testCopy(): void {
        $file = DIRECTORY_SEPARATOR . 'test.txt';
        
        $from = temp_dir(__NAMESPACE__);
        file_put_contents($from . $file, 'hello world');
        
        $to = temp_dir(__NAMESPACE__);
        
        FileSystem::copy($from, $to);
        
        $this->assertFileEquals($from . $file, $to . $file);
    }
}

