<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XML;

use PHPUnit\Framework\TestCase;

class LeanElementTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(LeanElement::class));
    }
}