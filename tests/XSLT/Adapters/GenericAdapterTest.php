<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

/**
 * GenericAdapterTest
 *
 * @see GenericAdapter
 */
final class GenericAdapterTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(GenericAdapter::class), "Failed to load class 'Slothsoft\Core\XSLT\Adapters\GenericAdapter'!");
    }
    
    /**
     *
     * @test
     */
    public function writeDocumentReturnsDocumentLoadedFromOutputFile(): void {
        $sut = new class() extends GenericAdapter {
            
            public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo {
                return $outputFile ?? FileInfoFactory::createFromString('<result>ok</result>');
            }
        };
        
        $document = $sut->writeDocument();
        
        $this->assertSame('result', $document->documentElement->tagName);
        $this->assertSame('ok', $document->documentElement->textContent);
    }
}
