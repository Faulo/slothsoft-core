<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Configuration;

use PHPUnit\Framework\TestCase;

class ConfigurationFieldTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ConfigurationField::class));
    }
}