<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

abstract class ZlibEncodeBase extends StreamFilterBase {
    
    /**
     * @return int
     */
    abstract static protected function getZlibEncoding(): int;
    
    private $compressor;
    
    /**
     * @return string
     */
    protected function processHeader(): string {
        $this->compressor = deflate_init(static::getZlibEncoding());
        return '';
    }
    
    /**
     * @param string $input
     * @return string
     */
    protected function processPayload(string $input): string {
        return deflate_add($this->compressor, $input, ZLIB_NO_FLUSH);
    }
    
    /**
     * @return string
     */
    protected function processFooter(): string {
        return deflate_add($this->compressor, '', ZLIB_FINISH);
    }
}
