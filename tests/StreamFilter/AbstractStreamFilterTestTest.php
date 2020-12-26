<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

use PHPUnit\Framework\TestCase;

class AbstractStreamFilterTestTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(AbstractStreamFilterTest::class));
    }
}