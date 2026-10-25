<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * ImageTest
 *
 * @see Image
 *
 */
final class ImageTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Image::class), "Failed to load class 'Slothsoft\Core\Image'!");
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testDisabledBackendsDoNotCreateOutput(): void {
        Image::$IRFANVIEW_ACTIVE = false;
        Image::$FFMPEG_ACTIVE = false;
        Image::$OPTIPNG_ACTIVE = false;
        
        $sourceFile = temp_dir(__METHOD__) . '/source.png';
        $destFile = temp_dir(__METHOD__) . '/output.png';
        
        $this->assertFalse(Image::convertFile($sourceFile, $destFile));
        $this->assertFalse(Image::scaleFile($sourceFile, $destFile, 10, 10));
        $this->assertFileDoesNotExist($destFile);
    }
}