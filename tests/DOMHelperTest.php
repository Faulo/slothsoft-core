<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;
use PHPUnit\Framework\Constraint\IsEqual;

/**
 * DOMHelperTest
 *
 * @see DOMHelper
 */
final class DOMHelperTest extends TestCase {
    
    private DOMHelper $sut;
    
    public function setUp(): void {
        $this->sut = new DOMHelper();
    }
    
    /**
     *
     * @test
     */
    public function testParseFragment() {
        $xml = '<xml/>';
        $fragment = $this->sut->parse($xml);
        
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
        
        $resultDoc = $this->sut->transformToDocument($dataDoc, $templateDoc, [
            'foo' => 'bar'
        ]);
        
        $this->assertInstanceOf(DOMElement::class, $resultDoc->documentElement);
        $this->assertEquals('output', $resultDoc->documentElement->tagName);
        $this->assertEquals('bar', $resultDoc->documentElement->getAttribute('foo'));
    }
    
    /**
     *
     * @dataProvider namespaceExamples
     */
    public function test_guessExtension(string $namespaceURI, string $expected) {
        $actual = DOMHelper::guessExtension($namespaceURI);
        
        $this->assertThat($actual, new IsEqual($expected));
    }
    
    public function namespaceExamples(): iterable {
        yield 'xml' => [
            '',
            'xml'
        ];
        yield 'sfm' => [
            DOMHelper::NS_FARAH_MODULE,
            'xml'
        ];
        yield 'html' => [
            DOMHelper::NS_HTML,
            'xhtml'
        ];
        yield 'svg' => [
            DOMHelper::NS_SVG,
            'svg'
        ];
        yield 'xslt' => [
            DOMHelper::NS_XSL,
            'xsl'
        ];
        yield 'xsd' => [
            DOMHelper::NS_XSD,
            'xsd'
        ];
    }
}