<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;

class GeneratorStreamTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(GeneratorStream::class));
    }
}