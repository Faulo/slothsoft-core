<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;

/**
 * ImageHelperTest
 *
 * @see ImageHelper
 */
class ImageHelperTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ImageHelper::class), "Failed to load class 'Slothsoft\Core\ImageHelper'!");
    }

    /**
     *
     * @dataProvider imageProvider
     * @test
     */
    public function testConvertToPng(string $inFile, string $expectedFile, ?int $alphaColorIndex) {
        $actualFile = FileInfoFactory::createTempFile();

        ImageHelper::convertToPng(FileInfoFactory::createFromPath($inFile), $actualFile, $alphaColorIndex);

        $this->assertFileEquals($expectedFile, (string) $actualFile);
    }

    public static function imageProvider(): array {
        return [
            'TGA to PNG' => [
                'test-files/ImageHelper/6.tga',
                'test-files/ImageHelper/6.png',
                0
            ]
        ];
    }
}