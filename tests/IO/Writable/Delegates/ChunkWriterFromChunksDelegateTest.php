<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Delegates;

use PHPUnit\Framework\TestCase;

class ChunkWriterFromChunksDelegateTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ChunkWriterFromChunksDelegate::class));
    }
}