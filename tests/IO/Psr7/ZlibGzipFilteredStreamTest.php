<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

final class ZlibGzipFilteredStreamTest extends AbstractFilteredStreamTestCase {
    
    protected function getInput(): string {
        return 'hello world';
    }
    
    protected function calculateExpectedResult(string $input): string {
        return gzencode($input, - 1, FORCE_GZIP);
    }
    
    protected function getFilterFactory(): FilteredStreamWriterInterface {
        return new ZlibFilteredStreamFactory(ZLIB_ENCODING_GZIP);
    }
}

