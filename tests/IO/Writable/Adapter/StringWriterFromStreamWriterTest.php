<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use PHPUnit\Framework\TestCase;

class StringWriterFromStreamWriterTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(StringWriterFromStreamWriter::class));
    }
}