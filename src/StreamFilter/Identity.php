<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

class Identity extends StreamFilterBase {

    protected function processHeader(): string {
        return '';
    }

    protected function processPayload(string $data): string {
        return $data;
    }

    protected function processFooter(): string {
        return '';
    }
}

