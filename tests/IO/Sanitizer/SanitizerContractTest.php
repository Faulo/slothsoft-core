<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Sanitizer;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\ContractAnalysis;

final class SanitizerContractTest extends TestCase {
    public function testInterfaceResultsPopulateConsumerArguments(): void {
        $sanitizers = [
            'text' => new StringSanitizer('fallback'),
            'number' => new IntegerSanitizer(7),
            'array' => new ArraySanitizer(['key' => 42]),
            'tokens' => new TokenSanitizer()
        ];
        $this->assertSame([
            'number' => 12,
            'tokens' => ['one', 'two'],
            'text' => 'fallback',
            'array' => ['key' => 42]
        ], ContractAnalysis::filterParameters($sanitizers, ['number' => '12', 'tokens' => 'one two']));
    }
}
