<?php
declare(strict_types = 1);

namespace Slothsoft\Core\Game;

/**
 * @deprecated Included for historical compatibility only. The Game API is out of support and should not be used in new code.
 */
final class Prime {
    
    public static function getPrimeList($size): array {
        $ret = [];
        for ($i = 1; $i > 0; $i++) {
            if (self::isPrime($i)) {
                $ret[] = $i;
            }
            if (count($ret) >= $size) {
                break;
            }
        }
        return $ret;
    }
    
    // http://stackoverflow.com/questions/16763322/a-formula-to-find-prime-numbers-in-a-loop
    public static function isPrime($num): bool {
        // 1 is not prime. See: http://en.wikipedia.org/wiki/Prime_number#Primality_of_one
        if ($num === 1) {
            return false;
        }
        
        // 2 is prime (the only even number that is prime)
        if ($num === 2) {
            return true;
        }
        
        /**
         * if the number is divisible by two, then it's not prime and it's no longer
         * needed to check other even numbers
         */
        if ($num % 2 === 0) {
            return false;
        }
        
        /**
         * Checks the odd numbers.
         * If any of them is a factor, then it returns false.
         * The sqrt can be an aproximation, hence just for the sake of
         * security, one rounds it to the next highest integer value.
         */
        for ($i = 3, $j = ceil(sqrt($num)); $i <= $j; $i += 2) {
            if ($num % $i === 0) {
                return false;
            }
        }
        return true;
    }
}
