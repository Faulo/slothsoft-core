<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

class ChunkEncodeTest extends AbstractStreamFilterTest {

    protected function getInput(): string {
        return 'hello world';
    }

    protected function calculateExpectedResult(string $input): string {
        return dechex(strlen($input)) . "\r\n$input\r\n0\r\n\r\n";
    }

    protected function getFilterClass(): string {
        return ChunkEncode::class;
    }
}

