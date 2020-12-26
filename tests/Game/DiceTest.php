<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Game;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see Dice
 *
 * @todo auto-generated
 */
class DiceTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Dice::class), "Failed to load class 'Slothsoft\Core\Game\Dice'!");
    }
}