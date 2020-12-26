<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT;

use PHPUnit\Framework\TestCase;

class XsltFactoryTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(XsltFactory::class));
    }
}