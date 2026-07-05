<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Writable\FilteredStreamWriterInterface;

abstract class AbstractFilteredStreamTestCase extends TestCase {
    
    /**
     * @return string
     */
    abstract protected function getInput(): string;
    
    /**
     * @param string $input
     * @return string
     */
    abstract protected function calculateExpectedResult(string $input): string;
    
    /**
     * @return FilteredStreamWriterInterface
     */
    abstract protected function getFilterFactory(): FilteredStreamWriterInterface;
    
    private StreamInterface $inputStream;
    
    private string $expectedResult;
    
    private FilteredStreamWriterInterface $factory;
    
    /**
     * @return void
     */
    public function setUp(): void {
        $input = $this->getInput();
        $this->inputStream = Utils::streamFor($input);
        $this->expectedResult = $this->calculateExpectedResult($input);
        $this->factory = $this->getFilterFactory();
    }
    
    /**
     * @return void
     */
    public function testReadStream(): void {
        $stream = $this->factory->toFilteredStream($this->inputStream);
        
        $actual = $stream->getContents();
        
        $this->assertThat($actual, new IsEqual($this->expectedResult));
    }
}

