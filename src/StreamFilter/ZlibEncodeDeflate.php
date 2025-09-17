<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

class ZlibEncodeDeflate extends ZlibEncodeBase {
    
    static protected function getZlibEncoding(): int {
        return ZLIB_ENCODING_DEFLATE;
    }
}

