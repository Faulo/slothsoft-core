<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use Psr\Http\Message\StreamInterface;

final class ZlibFilteredStream extends AbstractFilteredStream {
    
    private int $zlibCoding;
    
    /** @var mixed */
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
        $result = deflate_add($this->compressor, $data, ZLIB_NO_FLUSH);
        if ($result === false) {
            throw new \RuntimeException('Unable to compress stream payload.');
        }
        return $result;
    }
    
    /**
     * @return string
     */
    protected function processFooter(): string {
        $result = deflate_add($this->compressor, '', ZLIB_FINISH);
        if ($result === false) {
            throw new \RuntimeException('Unable to finish stream compression.');
        }
        return $result;
    }
}
