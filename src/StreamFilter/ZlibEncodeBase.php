<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

abstract class ZlibEncodeBase extends StreamFilterBase {
    
    /**
     * @return int
     */
    abstract static protected function getZlibEncoding(): int;
    
    /** @var mixed */
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
     * @throws \RuntimeException
     */
    protected function processPayload(string $input): string {
        $result = deflate_add($this->compressor, $input, ZLIB_NO_FLUSH);
        if ($result === false) {
            throw new \RuntimeException('Unable to compress stream payload.');
        }
        return $result;
    }
    
    /**
     * @return string
     * @throws \RuntimeException
     */
    protected function processFooter(): string {
        $result = deflate_add($this->compressor, '', ZLIB_FINISH);
        if ($result === false) {
            throw new \RuntimeException('Unable to finish stream compression.');
        }
        return $result;
    }
}
