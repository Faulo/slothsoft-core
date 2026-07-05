<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

final class Identity extends StreamFilterBase {
    
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
        return $input;
    }
    
    /**
     * @return string
     */
    protected function processFooter(): string {
        return '';
    }
}
