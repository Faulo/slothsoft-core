<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use DOMDocument;
use DOMElement;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;

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

        $templateDoc = new DOMDocument();
        $templateDoc->loadXML($this->createTemplateXml());

        $resultDoc = $this->sut->transformToDocument($dataDoc, $templateDoc, [
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(DOMElement::class, $resultDoc->documentElement);
        $this->assertEquals('output', $resultDoc->documentElement->tagName);
        $this->assertEquals('bar', $resultDoc->documentElement->getAttribute('foo'));
    }

    /**
     *
     * @test
     */
    public function transformToFileCreatesTempOutputWhenNoneIsGiven(): void {
        $dataDoc = new DOMDocument();
        $dataDoc->loadXML('<input/>');

        $templateDoc = new DOMDocument();
        $templateDoc->loadXML($this->createTemplateXml());

        $resultFile = $this->sut->transformToFile($dataDoc, $templateDoc, [
            'foo' => 'bar'
        ]);
        $resultDoc = DOMHelper::loadDocument((string) $resultFile);

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

    /**
     *
     * @test
     * @dataProvider normalizeDocumentExamples
     */
    public function normalizeDocumentUsesExpectedNamespacePrefixes(string $inputXml, string $expectedXml): void {
        $document = new DOMDocument();
        $document->loadXML($inputXml);

        $actual = $this->sut->normalizeDocument($document);

        $this->assertSame($expectedXml, $actual->saveXML($actual->documentElement));
    }

    public function normalizeDocumentExamples(): iterable {
        yield 'unknown namespace meaning is preserved' => [
            '<n1:a xmlns:n1="n"><n2:b xmlns:n2="n"/></n1:a>',
            '<n1:a xmlns:n1="n"><n1:b/></n1:a>'
        ];

        yield 'known svg namespace uses svg prefix' => [
            '<n1:svg xmlns:n1="' . DOMHelper::NS_SVG . '"><n2:path xmlns:n2="' . DOMHelper::NS_SVG . '"/></n1:svg>',
            '<svg:svg xmlns:svg="' . DOMHelper::NS_SVG . '"><svg:path/></svg:svg>'
        ];

        yield 'known html namespace uses html prefix' => [
            '<x:html xmlns:x="' . DOMHelper::NS_HTML . '"><y:body xmlns:y="' . DOMHelper::NS_HTML . '"/></x:html>',
            '<html:html xmlns:html="' . DOMHelper::NS_HTML . '"><html:body/></html:html>'
        ];

        yield 'known namespace with default root does not add prefix' => [
            '<svg xmlns="' . DOMHelper::NS_SVG . '"><path/></svg>',
            '<svg xmlns="' . DOMHelper::NS_SVG . '"><path/></svg>'
        ];

        yield 'known namespace with default root keeps namespace meaning' => [
            '<svg xmlns="' . DOMHelper::NS_SVG . '"><n1:path xmlns:n1="' . DOMHelper::NS_SVG . '"/></svg>',
            '<svg xmlns="' . DOMHelper::NS_SVG . '"><path/></svg>'
        ];

        yield 'known namespace with prefixed root keeps child namespace meaning' => [
            '<n1:svg xmlns:n1="' . DOMHelper::NS_SVG . '"><path xmlns="' . DOMHelper::NS_SVG . '"/></n1:svg>',
            '<svg:svg xmlns:svg="' . DOMHelper::NS_SVG . '"><svg:path/></svg:svg>'
        ];

        yield 'known xlink attribute namespace uses xlink prefix' => [
            '<svg:use xmlns:svg="' . DOMHelper::NS_SVG . '" xmlns:n1="' . DOMHelper::NS_XLINK . '" n1:href="#icon"/>',
            '<svg:use xmlns:svg="' . DOMHelper::NS_SVG . '" xmlns:xlink="' . DOMHelper::NS_XLINK . '" xlink:href="#icon"/>'
        ];

        yield 'known prefix bound to unknown namespace stays untouched' => [
            '<html:nothtml xmlns:html="not-the-xhtml-ns"/>',
            '<html:nothtml xmlns:html="not-the-xhtml-ns"/>'
        ];

        yield 'redundant default namespace declaration is removed' => [
            '<a xmlns=""><b xmlns=""/></a>',
            '<a><b/></a>'
        ];

        yield 'redundant namespace declaration is removed' => [
            '<a xmlns="urn:example"><b xmlns="urn:example"/></a>',
            '<a xmlns="urn:example"><b/></a>'
        ];

        yield 'prefixed namespace can serialize through default namespace' => [
            '<a xmlns="urn:example"><ns:b xmlns:ns="urn:example"/></a>',
            '<a xmlns="urn:example"><b/></a>'
        ];
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

    private function createTemplateXml(): string {
        /** @noinspection HtmlDeprecatedAttribute,HtmlUnknownAttribute */
        return '<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">' .
            '<xsl:param name="foo"/>' .
            '<xsl:template match="input">' .
            '<output foo="{$foo}"/>' .
            '</xsl:template>' .
            '</xsl:stylesheet>';
    }
}
