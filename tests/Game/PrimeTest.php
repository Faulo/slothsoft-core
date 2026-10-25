<?php
declare(strict_types = 1);

namespace Slothsoft\Core\Game;

use PHPUnit\Framework\TestCase;

/**
 * PrimeTest
 *
 * @see Prime
 */
final class PrimeTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Prime::class), "Failed to load class 'Slothsoft\Core\Game\Prime'!");
    }
    
    public function testPrimeList(): void {
        $this->assertSame([2, 3, 5, 7, 11], Prime::getPrimeList(5));
    }
    
}