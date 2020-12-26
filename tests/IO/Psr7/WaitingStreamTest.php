<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;

class WaitingStreamTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(WaitingStream::class));
    }
}