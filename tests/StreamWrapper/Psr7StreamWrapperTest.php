<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamWrapper;

use PHPUnit\Framework\TestCase;

class Psr7StreamWrapperTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Psr7StreamWrapper::class));
    }
}