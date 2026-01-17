<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Game;

use PHPUnit\Framework\TestCase;

/**
 * PrimeTest
 *
 * @see Prime
 *
 * @todo auto-generated
 */
class PrimeTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Prime::class), "Failed to load class 'Slothsoft\Core\Game\Prime'!");
    }
}