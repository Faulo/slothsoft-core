<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Inputs;

use PHPUnit\Framework\TestCase;

class DocumentInputTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(DocumentInput::class));
    }
}