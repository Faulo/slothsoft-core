<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

final class ZlibDeflateFilteredStreamTest extends AbstractFilteredStreamTestCase {
    
    protected function getInput(): string {
        return 'hello world';
    }
    
    protected function calculateExpectedResult(string $input): string {
        $result = gzencode($input, -1, FORCE_DEFLATE);
        $this->assertIsString($result);
        return $result;
    }
    
    protected function getFilterFactory(): FilteredStreamWriterInterface {
        return new ZlibFilteredStreamFactory(ZLIB_ENCODING_DEFLATE);
    }
}

