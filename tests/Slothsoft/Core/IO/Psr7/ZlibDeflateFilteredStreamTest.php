<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

class ZlibDeflateFilteredStreamTest extends AbstractFilteredStreamTest
{

    protected function getInput(): string
    {
        return 'hello world';
    }

    protected function calculateExpectedResult(string $input): string
    {
        return gzencode($input, - 1, FORCE_DEFLATE);
    }

    protected function getFilterFactory(): FilteredStreamWriterInterface
    {
        return new ZlibFilteredStreamFactory(ZLIB_ENCODING_DEFLATE);
    }
}

