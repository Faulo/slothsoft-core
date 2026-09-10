<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use DOMDocument;
use DOMDocumentType;
use DOMText;
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\XSLT\Inputs\DocumentInput;

/**
 * XsltProcessorAdapterTest
 *
 * @see XsltProcessorAdapter
 */
final class XsltProcessorAdapterTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(XsltProcessorAdapter::class), "Failed to load class 'Slothsoft\Core\XSLT\Adapters\XsltProcessorAdapter'!");
    }

    /**
     * @test
     * @dataProvider validDocumentTypes
     */
    public function writeDocumentConvertsValidDocumentTypeText(
        string $documentType,
        string $expectedName,
        string $expectedPublicId,
        string $expectedSystemId,
        string $expectedInternalSubset
    ): void {
        $result = $this->createAdapter($documentType)->writeDocument();

        $this->assertInstanceOf(DOMDocumentType::class, $result->doctype);
        $this->assertSame($expectedName, $result->doctype->name);
        $this->assertSame($expectedPublicId, $result->doctype->publicId);
        $this->assertSame($expectedSystemId, $result->doctype->systemId);
        $this->assertSame($expectedInternalSubset, trim((string) $result->doctype->internalSubset));
        $this->assertSame('root', $result->documentElement->tagName);
    }

    public function validDocumentTypes(): iterable {
        yield 'HTML5' => [
            '<!DOCTYPE html>',
            'html',
            '',
            '',
            ''
        ];
        yield 'system identifier' => [
            '<!DOCTYPE root SYSTEM "about:legacy-compat">',
            'root',
            '',
            'about:legacy-compat',
            ''
        ];
        yield 'public identifier' => [
            '<!DOCTYPE root PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "about:legacy-compat">',
            'root',
            '-//W3C//DTD XHTML 1.0 Strict//EN',
            'about:legacy-compat',
            ''
        ];
        yield 'internal subset' => [
            '<!DOCTYPE root [<!ELEMENT root ANY>]>',
            'root',
            '',
            '',
            '<!ELEMENT root ANY>'
        ];
    }

    /**
     * @test
     */
    public function writeDocumentConvertsDocumentTypeBeforeOtherPrologNodes(): void {
        $result = $this->createAdapter('<!DOCTYPE root>', '<xsl:comment>comment</xsl:comment>')->writeDocument();

        $this->assertInstanceOf(DOMDocumentType::class, $result->doctype);
        $this->assertSame('comment', $result->doctype->nextSibling->nodeValue);
        $this->assertSame('root', $result->documentElement->tagName);
    }

    /**
     * @test
     * @dataProvider invalidDocumentTypes
     */
    public function writeDocumentPreservesTextThatIsNotExactlyOneValidDocumentType(string $text): void {
        $result = $this->createAdapter($text)->writeDocument();

        $this->assertNull($result->doctype);
        $this->assertInstanceOf(DOMText::class, $result->documentElement->previousSibling);
        $this->assertSame($text, $result->documentElement->previousSibling->nodeValue);
    }

    public function invalidDocumentTypes(): iterable {
        yield 'ordinary text' => [
            'not a doctype'
        ];
        yield 'invalid declaration' => [
            '<!DOCTYPE>'
        ];
        yield 'doctype and another node' => [
            '<!DOCTYPE root><!-- comment -->'
        ];
    }

    /**
     * @test
     */
    public function writeDocumentPreservesDocumentTypeSplitAcrossTextNodes(): void {
        $result = $this->createAdapter(
            '<!DOCTYPE ',
            '<xsl:comment>separator</xsl:comment>' .
            '<xsl:text disable-output-escaping="yes">root&gt;</xsl:text>'
        )->writeDocument();

        $this->assertNull($result->doctype);
        $this->assertSame('root>', $result->documentElement->previousSibling->nodeValue);
    }

    private function createAdapter(string $text, string $followingProlog = ''): XsltProcessorAdapter {
        $source = new DOMDocument();
        $source->loadXML('<input/>');

        $template = new DOMDocument();
        $template->loadXML(
            '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' .
            '<xsl:template match="/*">' .
            '<xsl:text disable-output-escaping="yes">' . htmlspecialchars($text, ENT_XML1) . '</xsl:text>' .
            $followingProlog .
            '<root/>' .
            '</xsl:template>' .
            '</xsl:stylesheet>'
        );

        $adapter = new XsltProcessorAdapter();
        $adapter->setSource(new DocumentInput($source));
        $adapter->setTemplate(new DocumentInput($template));
        return $adapter;
    }
}
