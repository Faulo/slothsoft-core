<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

/**
 * SaxonProcessorAdapterTest
 *
 * @see SaxonProcessorAdapter
 */
final class SaxonProcessorAdapterTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(SaxonProcessorAdapter::class), "Failed to load class 'Slothsoft\Core\XSLT\Adapters\SaxonProcessorAdapter'!");
    }
    
    /**
     *
     * @test
     */
    public function writeDocumentReturnsDocumentLoadedFromOutputFile(): void {
        $sut = new class() extends SaxonProcessorAdapter {
            
            public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo {
                return $outputFile ?? FileInfoFactory::createFromString('<result>ok</result>');
            }
        };
        
        $document = $sut->writeDocument();
        
        $this->assertSame('result', $document->documentElement->tagName);
        $this->assertSame('ok', $document->documentElement->textContent);
    }
}
