<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

use PHPUnit\Framework\TestCase;

abstract class AbstractStreamFilterTest extends TestCase
{

    abstract protected function getInput(): string;

    abstract protected function calculateExpectedResult(string $input): string;

    abstract protected function getFilterClass(): string;

    private $streamId;

    private $tempFile;

    private $input;

    private $expectedResult;

    public function setUp(): void
    {
        $this->streamId = uniqid(md5($this->getFilterClass()));
        $this->tempFile = tempnam(sys_get_temp_dir(), __CLASS__);
        $this->input = $this->getInput();
        $this->expectedResult = $this->calculateExpectedResult($this->input);
        
        stream_filter_register($this->streamId, $this->getFilterClass());
    }

    public function testWriteToResource()
    {
        $resource = fopen($this->tempFile, 'wb');
        stream_filter_append($resource, $this->streamId, STREAM_FILTER_WRITE);
        fwrite($resource, $this->input);
        fclose($resource);
        
        $actualResult = file_get_contents($this->tempFile);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }

    public function testWriteToPath()
    {
        $path = "php://filter/write=$this->streamId/resource=$this->tempFile";
        file_put_contents($path, $this->input);
        
        $actualResult = file_get_contents($this->tempFile);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }

    public function testReadFromResource()
    {
        file_put_contents($this->tempFile, $this->input);
        
        $resource = fopen($this->tempFile, 'rb');
        stream_filter_append($resource, $this->streamId, STREAM_FILTER_READ);
        $actualResult = stream_get_contents($resource);
        fclose($resource);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }

    public function testReadFromPath()
    {
        file_put_contents($this->tempFile, $this->input);
        
        $path = "php://filter/read=$this->streamId/resource=$this->tempFile";
        $actualResult = file_get_contents($path);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }
}

