<?php
declare(strict_types = 1);

namespace Slothsoft\Core\Game;

/**
 * Dice rolling helper.
 *
 * @author Daniel Schulz
 * @since 2013-09-20
 * @deprecated Included for historical compatibility only. The Game API is out of support and should not be used in new code.
 */
final class Dice {
    
    protected $sidesCount;
    
    protected $diceCount;
    
    public function __construct($sidesCount, $diceCount = 1) {
        $this->sidesCount = max((int) $sidesCount, 1);
        $this->diceCount = max((int) $diceCount, 1);
    }
    
    public function getMax() {
        return $this->diceCount * $this->sidesCount;
    }
    
    public function getMin() {
        return $this->diceCount;
    }
    
    public function getAverage() {
        $ret = $this->sidesCount;
        $ret++;
        $ret *= $this->diceCount;
        $ret /= 2;
        return $ret;
    }
    
    public function enclose($value) {
        return min($this->getMax(), max($this->getMin(), $value));
    }
    
    public function roll(): int {
        $ret = 0;
        for ($i = 0; $i < $this->diceCount; $i++) {
            $ret += $this->rand(1, $this->sidesCount);
        }
        return $ret;
    }
    
    public function rollImpossible(): bool {
        return $this->rollLower($this->getMin());
    }
    
    // returns true if these dice rolled at least as high as $value
    // e.g. for ETW0, saving throws
    public function rollHigher($value): bool {
        return $this->roll() >= $value;
    }
    
    // returns true if these dice rolled at most as high as $value
    // e.g. for skill checks, attribute checks, percentages
    public function rollLower($value): bool {
        return $this->roll() <= $value;
    }
    
    public function rollEqual($value): bool {
        return $value === $this->roll();
    }
    
    protected function rand($min, $max): int {
        return rand($min, $max);
    }
}
