<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * ServerEnvironmentTest
 *
 * @see ServerEnvironment
 *
 * @todo auto-generated
 */
class ServerEnvironmentTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(ServerEnvironment::class), "Failed to load class 'Slothsoft\Core\ServerEnvironment'!");
    }
}