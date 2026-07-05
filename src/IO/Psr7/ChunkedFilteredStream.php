<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

final class ChunkedFilteredStream extends AbstractFilteredStream {
    
    /**
     * @return string
     */
    protected function processHeader(): string {
        return '';
    }
    
    /**
     * @param string $data
     * @return string
     */
    protected function processPayload(string $data): string {
        return $data === '' ? '' : dechex(strlen($data)) . "\r\n" . $data . "\r\n";
    }
    
    /**
     * @return string
     */
    protected function processFooter(): string {
        return "0\r\n\r\n";
    }
}
