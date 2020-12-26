<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Game;

use PHPUnit\Framework\TestCase;

class NameTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Name::class));
    }
}