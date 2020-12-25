<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

class ChunkedFilteredStreamTest extends AbstractFilteredStreamTest {

    protected function getInput(): string {
        return 'hello world';
    }

    protected function calculateExpectedResult(string $input): string {
        return dechex(strlen($input)) . "\r\n$input\r\n0\r\n\r\n";
    }

    protected function getFilterFactory(): FilteredStreamWriterInterface {
        return new ChunkedFilteredStreamFactory();
    }
}

