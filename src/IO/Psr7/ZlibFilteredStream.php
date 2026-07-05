<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;

final class ZlibFilteredStream extends AbstractFilteredStream {
    
    private int $zlibCoding;
    
    private $compressor;
    
    /**
     * @param StreamInterface $stream
     * @param int $zlibCoding
     */
    public function __construct(StreamInterface $stream, int $zlibCoding) {
        parent::__construct($stream);
        $this->zlibCoding = $zlibCoding;
    }
    
    /**
     * @return string
     */
    protected function processHeader(): string {
        $this->compressor = deflate_init($this->zlibCoding);
        return '';
    }
    
    /**
     * @param string $data
     * @return string
     */
    protected function processPayload(string $data): string {
        return deflate_add($this->compressor, $data, ZLIB_NO_FLUSH);
    }
    
    /**
     * @return string
     */
    protected function processFooter(): string {
        return deflate_add($this->compressor, '', ZLIB_FINISH);
    }
}
