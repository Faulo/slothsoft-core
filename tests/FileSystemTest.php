<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;

class FileSystemTest extends TestCase {

    public function testAsNode(): void {
        $document = FileSystem::asNode(__DIR__);

        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertInstanceOf(DOMElement::class, $document->documentElement);
    }

    /**
     *
     * @dataProvider createSanitizedFilenames
     */
    public function testFilenameSanitize($input, $output): void {
        $this->assertEquals($output, FileSystem::filenameSanitize($input));
    }

    public function createSanitizedFilenames(): iterable {
        return [
            [
                'A',
                'A'
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

    public function testRemoveDirIncludingRoot(): void {
        $directory = temp_dir(__NAMESPACE__);

        mkdir("$directory/A", 0777, true);
        file_put_contents("$directory/A/B", 'test');

        $this->assertFileExists("$directory/A/B");

        FileSystem::removeDir($directory, false);

        $this->assertFileNotExists("$directory/A/B");
        $this->assertDirectoryNotExists("$directory/A");
        $this->assertDirectoryNotExists($directory);
    }

    public function testRemoveDirExcludingRoot(): void {
        $directory = temp_dir(__NAMESPACE__);

        mkdir("$directory/A", 0777, true);
        file_put_contents("$directory/A/B", 'test');

        $this->assertFileExists("$directory/A/B");

        FileSystem::removeDir($directory, true);

        $this->assertFileNotExists("$directory/A/B");
        $this->assertDirectoryNotExists("$directory/A");
        $this->assertDirectoryExists($directory);
    }

    /**
     *
     * @dataProvider commandExamples
     */
    public function testCommandExist(string $command, bool $expected): void {
        $this->assertEquals($expected, FileSystem::commandExists($command));
    }

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
}

