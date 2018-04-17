<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

class ChunkEncode extends StreamFilterBase
{

    protected function processHeader(): string
    {
        return '';
    }

    protected function processPayload(string $data): string
    {
        return dechex(strlen($data)) . "\r\n" . $data . "\r\n";
    }

    protected function processFooter(): string
    {
        return "0\r\n\r\n";
    }
}

