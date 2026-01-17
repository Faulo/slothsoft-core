<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * ImageTest
 *
 * @see Image
 *
 * @todo auto-generated
 */
final class ImageTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Image::class), "Failed to load class 'Slothsoft\Core\Image'!");
    }
}