<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DOMTests;

use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use DOMNode;

final class DOMNodeEqualTo extends Constraint {
    
    private DOMNode $expected;
    
    private string $expectedText;
    
    public function __construct(DOMNode $expected) {
        $this->expected = $expected;
        $this->expectedText = self::normalizeNodeAsText($expected);
    }
    
    public function toString(): string {
        return 'is XML-equal to expected DOMNode';
    }
    
    protected function matches($other): bool {
        if (! $other instanceof DOMNode) {
            return false;
        }
        
        $otherText = self::normalizeNodeAsText($other);
        
        return $this->expectedText === $otherText;
    }
    
    protected function failureDescription($other): string {
        return 'the provided DOMNode ' . $this->toString();
    }
    
    protected function additionalFailureDescription($other): string {
        $otherText = self::normalizeNodeAsText($other);
        
        return (new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n")))->diff($this->expectedText, $otherText);
    }
    
    private static function normalizeNodeAsText(DOMNode $node): string {
        return implode(PHP_EOL, [
            ...self::normalizeNode($node)
        ]);
    }
    
    private static function normalizeNode(DOMNode $node, int $depth = 0): iterable {
        switch ($node->nodeType) {
            case XML_DOCUMENT_NODE:
                if ($node->documentElement) {
                    yield from self::normalizeNode($node->documentElement, $depth);
                }
                break;
            case XML_DOCUMENT_FRAG_NODE:
                foreach ($node->childNodes as $child) {
                    yield from self::normalizeNode($child, $depth);
                }
                break;
            case XML_ELEMENT_NODE:
                yield sprintf('%s<%s', self::printDepth($depth), self::printName($node));
                $depth ++;
                
                $attributes = [];
                foreach ($node->attributes as $child) {
                    $attributes[self::printName($child)] = $child;
                }
                ksort($attributes);
                foreach ($attributes as $child) {
                    yield from self::normalizeNode($child, $depth);
                }
                
                yield sprintf('%s>', self::printDepth($depth));
                
                foreach ($node->childNodes as $child) {
                    yield from self::normalizeNode($child, $depth);
                }
                break;
            case XML_ATTRIBUTE_NODE:
                yield sprintf('%s%s=%s', self::printDepth($depth), self::printName($node), json_encode($node->nodeValue));
                break;
            case XML_TEXT_NODE:
            case XML_CDATA_SECTION_NODE:
                $text = self::normalizeSpace($node->nodeValue);
                if ($text !== '') {
                    yield sprintf('%s%s', self::printDepth($depth), json_encode($text));
                }
                break;
        }
    }
    
    private static function printName(DOMNode $node): string {
        return $node->namespaceURI ? "$node->namespaceURI $node->localName" : $node->localName;
    }
    
    private static function printDepth(int $depth): string {
        return str_pad('', $depth, '  ');
    }
    
    private static function normalizeSpace(?string $text): string {
        $text = (string) $text;
        $text = preg_replace('~[ \t\r\n]+~', ' ', $text);
        $text = trim($text, ' ');
        return $text;
    }
}
