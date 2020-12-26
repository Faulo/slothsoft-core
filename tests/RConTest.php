<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class RConTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(RCon::class));
    }
}