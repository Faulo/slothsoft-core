<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;
use Slothsoft\Core\IO\FileInfoFactory;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;

/**
 * DOMWriterFromFileWriterTest
 *
 * @see DOMWriterFromFileWriter
 */
final class DOMWriterFromFileWriterTest extends TestCase implements FileWriterInterface {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DOMWriterFromFileWriter::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Adapter\DOMWriterFromFileWriter'!");
    }
    
    public function toFile(): SplFileInfo {
        return FileInfoFactory::createFromString('<xml/>');
    }
    
    public function test_loadFile() {
        $sut = new DOMWriterFromFileWriter($this);
        
        $actual = $sut->toDocument();
        
        $this->assertThat($actual->documentElement->tagName, new IsEqual('xml'));
    }
    
    public function test_cacheDocument() {
        $sut = new DOMWriterFromFileWriter($this);
        
        $expected = $sut->toDocument();
        $actual = $sut->toDocument();
        
        $this->assertThat($actual, new IsIdentical($expected));
    }
}