<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

use PHPUnit\Framework\TestCase;

class ChunkEncodeTest extends TestCase
{
    private $streamId;
    private $tempFile;
    private $input;
    private $expectedResult;
    
    public function setUp() {
        $this->streamId = uniqid();
        $this->tempFile = tempnam(sys_get_temp_dir(), __CLASS__);
        $this->input = 'hello world';
        $this->expectedResult = dechex(strlen($this->input)) . "\r\n$this->input\r\n0\r\n\r\n";
        
        stream_filter_register($this->streamId, ChunkEncode::class);
    }
    
    private function getTempFile() : string {
        return tempnam(sys_get_temp_dir(), __CLASS__);
    }
    
    public function testWriteToResource() {
        $this->tempFile = $this->getTempFile();
        
        $handle = fopen($this->tempFile, 'wb');
        stream_filter_append($handle, $this->streamId, STREAM_FILTER_WRITE);
        fwrite($handle, $this->input);
        fclose($handle);
        
        $actualResult = file_get_contents($this->tempFile);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }
    public function testWriteToPath() {
        $this->input = 'hello world';
        $this->expectedResult = dechex(strlen($this->input)) . "\r\n$this->input\r\n0\r\n\r\n";
        $this->tempFile = $this->getTempFile();
        
        file_put_contents("php://filter/write=$this->streamId/resource=$this->tempFile", $this->input);
        $actualResult = file_get_contents($this->tempFile);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }
    
    public function testReadFromResource() {
        $this->input = 'hello world';
        $this->expectedResult = dechex(strlen($this->input)) . "\r\n$this->input\r\n0\r\n\r\n";
        $this->tempFile = $this->getTempFile();
        
        file_put_contents($this->tempFile, $this->input);
        
        $handle = fopen($this->tempFile, 'rb');
        stream_filter_append($handle, $this->streamId, STREAM_FILTER_READ);
        $actualResult = stream_get_contents($handle);
        fclose($handle);
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }
    public function testReadFromPath() {
        $this->input = 'hello world';
        $this->expectedResult = dechex(strlen($this->input)) . "\r\n$this->input\r\n0\r\n\r\n";
        $this->tempFile = $this->getTempFile();
        
        file_put_contents($this->tempFile, $this->input);
        $actualResult = file_get_contents("php://filter/read=$this->streamId/resource=$this->tempFile");
        
        $this->assertEquals($this->expectedResult, $actualResult);
    }
}

