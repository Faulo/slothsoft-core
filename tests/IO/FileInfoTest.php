<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO;

use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(FileInfo::class));
    }
}