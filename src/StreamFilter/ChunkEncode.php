<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

final class ChunkEncode extends StreamFilterBase {
    
    /**
     * @return string
     */
    protected function processHeader(): string {
        return '';
    }
    
    /**
     * @param string $input
     * @return string
     */
    protected function processPayload(string $input): string {
        return dechex(strlen($input)) . "\r\n" . $input . "\r\n";
    }
    
    /**
     * @return string
     */
    protected function processFooter(): string {
        return "0\r\n\r\n";
    }
}
