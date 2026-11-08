<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use Slothsoft\Core\IO\Sanitizer\SanitizerInterface;
use Slothsoft\Core\StreamFilter\StreamFilterInterface;

/**
 * Analysis fixtures for consumers using interfaces rather than concrete classes.
 * PHPStan analyses this file through its existing tests path; PHPUnit does not execute it.
 */
final class ContractAnalysis {
    /**
     * Farah's parameter filter reads both results into its argument map.
     *
     * @param array<string, SanitizerInterface> $sanitizers
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    public static function filterParameters(array $sanitizers, array $values): array {
        foreach ($sanitizers as $name => $sanitizer) {
            $values[$name] = isset($values[$name]) ? $sanitizer->apply($values[$name]) : $sanitizer->getDefault();
        }
        return $values;
    }
    
    public static function createFilter(StreamFilterInterface $filter): bool {
        return $filter->onCreate();
    }
    
    /**
     * @param resource $in
     * @param resource $out
     */
    public static function filterBuckets(StreamFilterInterface $filter, $in, $out): int {
        $consumed = 0;
        return $filter->filter($in, $out, $consumed, false);
    }
}
