<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;

class DOMHelperTest extends TestCase {
    
    private $dom;
    
    public function setUp(): void {
        $this->dom = new DOMHelper();
    }
    
    /**
     *
     * @test
     */
    public function testParseFragment() {
        $xml = '<xml/>';
        $fragment = $this->dom->parse($xml);
        
        $this->assertInstanceOf(DOMElement::class, $fragment->firstChild);
        $this->assertEquals('xml', $fragment->firstChild->tagName);
    }
    
    /**
     *
     * @test
     */
    public function testTransformDocuments() {
        $dataDoc = new DOMDocument();
        $dataDoc->loadXML('<input/>');
        
        $templateXml = <<<EOT
        <xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
            <xsl:param name="foo"/>
            <xsl:template match="input">
                <output foo="{\$foo}"/>
            </xsl:template>
        </xsl:stylesheet>
        EOT;
        $templateDoc = new DOMDocument();
        $templateDoc->loadXML($templateXml);
        
        $resultDoc = $this->dom->transformToDocument($dataDoc, $templateDoc, [
            'foo' => 'bar'
        ]);
        
        $this->assertInstanceOf(DOMElement::class, $resultDoc->documentElement);
        $this->assertEquals('output', $resultDoc->documentElement->tagName);
        $this->assertEquals('bar', $resultDoc->documentElement->getAttribute('foo'));
    }
}