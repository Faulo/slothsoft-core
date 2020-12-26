<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Identity::class));
    }
}