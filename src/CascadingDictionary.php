<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

class CascadingDictionary implements ArrayAccess, IteratorAggregate {
    
    private array $values = [];
    
    private callable $comparer;
    
    public function __construct() {
        $this->comparer = function (string $a, string $b): int {
            return strlen($b) - strlen($a);
        };
    }
    
    public function offsetExists($offset): bool {
        foreach (array_keys($this->values) as $key) {
            if (strpos($offset, $key) === 0) {
                return true;
            }
        }
        return false;
    }
    
    #[\ReturnTypeWillChange]
    public function &offsetGet($offset) {
        foreach (array_keys($this->values) as $key) {
            if (strpos($offset, $key) === 0) {
                return $this->values[$key];
            }
        }
        return $this->values[$offset];
    }
    
    public function offsetSet($offset, $value): void {
        if (! is_string($offset)) {
            trigger_error('CascadingDictionary requires keys to be strings!', E_USER_WARNING);
            return;
        }
        $this->values[$offset] = $value;
        
        uksort($this->values, $this->comparer);
    }
    
    public function offsetUnset($offset): void {
        unset($this->values[$offset]);
    }
    
    public function getIterator(): Traversable {
        return new ArrayIterator($this->values);
    }
}

