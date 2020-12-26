<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use PHPUnit\Framework\TestCase;

class StringWriterMemoryCacheTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(StringWriterMemoryCache::class));
    }
}