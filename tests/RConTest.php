<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * RConTest
 *
 * @see RCon
 *
 * @todo auto-generated
 */
class RConTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(RCon::class), "Failed to load class 'Slothsoft\Core\RCon'!");
    }
}