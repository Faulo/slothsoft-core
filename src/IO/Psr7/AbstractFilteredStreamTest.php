<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

abstract class AbstractFilteredStreamTest extends TestCase {

    abstract protected function getInput(): string;

    abstract protected function calculateExpectedResult(string $input): string;

    abstract protected function getFilterFactory(): FilteredStreamWriterInterface;

    private $inputStream;

    private $expectedResult;

    private $factory;

    public function setUp(): void {
        $input = $this->getInput();
        $this->inputStream = stream_for($input);
        $this->expectedResult = $this->calculateExpectedResult($input);
        $this->factory = $this->getFilterFactory();
    }

    public function testReadStream() {
        $stream = $this->factory->toFilteredStream($this->inputStream);

        $actualResult = $stream->getContents();

        $this->assertEquals($this->expectedResult, $actualResult);
    }
}

