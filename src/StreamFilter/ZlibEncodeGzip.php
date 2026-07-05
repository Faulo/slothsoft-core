<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

final class ZlibEncodeGzip extends ZlibEncodeBase {
    
    /**
     * @return int
     */
    static protected function getZlibEncoding(): int {
        return ZLIB_ENCODING_GZIP;
    }
}

