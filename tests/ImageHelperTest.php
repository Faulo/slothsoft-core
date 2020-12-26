<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class ImageHelperTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ImageHelper::class));
    }
}