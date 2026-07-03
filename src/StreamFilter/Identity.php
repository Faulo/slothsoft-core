<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

class Identity extends StreamFilterBase {
    
    protected function processHeader(): string {
        return '';
    }
    
    protected function processPayload(string $input): string {
        return $input;
    }
    
    protected function processFooter(): string {
        return '';
    }
}
