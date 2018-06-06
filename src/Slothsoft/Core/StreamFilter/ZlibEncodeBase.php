<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

abstract class ZlibEncodeBase extends StreamFilterBase
{

    abstract static protected function getZlibEncoding(): int;

    private $compressor;

    protected function processHeader(): string
    {
        $this->compressor = deflate_init(static::getZlibEncoding());
        return '';
    }

    protected function processPayload(string $data): string
    {
        return deflate_add($this->compressor, $data, ZLIB_NO_FLUSH);
    }

    protected function processFooter(): string
    {
        return deflate_add($this->compressor, '', ZLIB_FINISH);
    }
}

