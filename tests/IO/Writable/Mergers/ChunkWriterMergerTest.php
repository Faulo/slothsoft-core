<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Mergers;

use PHPUnit\Framework\TestCase;

class ChunkWriterMergerTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ChunkWriterMerger::class));
    }
}