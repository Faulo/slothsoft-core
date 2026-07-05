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
    
    /**
     * @param mixed $sidesCount
     * @param mixed $diceCount
     */
    public function __construct($sidesCount, $diceCount = 1) {
        $this->sidesCount = max((int) $sidesCount, 1);
        $this->diceCount = max((int) $diceCount, 1);
    }
    
    /**
     * @return mixed
     */
    public function getMax() {
        return $this->diceCount * $this->sidesCount;
    }
    
    /**
     * @return mixed
     */
    public function getMin() {
        return $this->diceCount;
    }
    
    /**
     * @return mixed
     */
    public function getAverage() {
        $ret = $this->sidesCount;
        $ret++;
        $ret *= $this->diceCount;
        $ret /= 2;
        return $ret;
    }
    
    /**
     * @param mixed $value
     * @return mixed
     */
    public function enclose($value) {
        return min($this->getMax(), max($this->getMin(), $value));
    }
    
    /**
     * @return int
     */
    public function roll(): int {
        $ret = 0;
        for ($i = 0; $i < $this->diceCount; $i++) {
            $ret += $this->rand(1, $this->sidesCount);
        }
        return $ret;
    }
    
    /**
     * @return bool
     */
    public function rollImpossible(): bool {
        return $this->rollLower($this->getMin());
    }
    
    // returns true if these dice rolled at least as high as $value
    // e.g. for ETW0, saving throws
    /**
     * @param mixed $value
     * @return bool
     */
    public function rollHigher($value): bool {
        return $this->roll() >= $value;
    }
    
    // returns true if these dice rolled at most as high as $value
    // e.g. for skill checks, attribute checks, percentages
    /**
     * @param mixed $value
     * @return bool
     */
    public function rollLower($value): bool {
        return $this->roll() <= $value;
    }
    
    /**
     * @param mixed $value
     * @return bool
     */
    public function rollEqual($value): bool {
        return $value === $this->roll();
    }
    
    /**
     * @param mixed $min
     * @param mixed $max
     * @return int
     */
    protected function rand($min, $max): int {
        return rand($min, $max);
    }
}
