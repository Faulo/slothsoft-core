<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

class ZlibEncodeDeflateTest extends AbstractStreamFilterTest {
    
    protected function getInput(): string {
        return 'hello world';
    }
    
    protected function calculateExpectedResult(string $input): string {
        $result = gzencode($input, -1, FORCE_DEFLATE);
        $this->assertIsString($result);
        return $result;
    }
    
    protected function getFilterClass(): string {
        return ZlibEncodeDeflate::class;
    }
}

