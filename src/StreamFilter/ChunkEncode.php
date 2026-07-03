<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

final class ChunkEncode extends StreamFilterBase {
    
    protected function processHeader(): string {
        return '';
    }
    
    protected function processPayload(string $input): string {
        return dechex(strlen($input)) . "\r\n" . $input . "\r\n";
    }
    
    protected function processFooter(): string {
        return "0\r\n\r\n";
    }
}
