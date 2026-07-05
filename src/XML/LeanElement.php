<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XML;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use Ds\Vector;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterDocumentFromElementTrait;

/**
 * Lightweight DOM element tree that can write itself back to a DOM document.
 *
 * @author Daniel Schulz
 * @since 2018-03-01
 */
final class LeanElement implements DOMWriterInterface {
    use DOMWriterDocumentFromElementTrait;
    
    /**
     * @param DOMNodeList $domNodeList
     * @return array
     */
    public static function createTreeListFromDOMNodeList(DOMNodeList $domNodeList): array {
        $ret = [];
        foreach ($domNodeList as $domNode) {
            if ($domNode instanceof DOMElement) {
                $ret[] = self::createTreeFromDOMElement($domNode);
            }
        }
        return $ret;
    }
    
    /**
     * @param DOMDocument $domDocument
     * @return LeanElement
     */
    public static function createTreeFromDOMDocument(DOMDocument $domDocument): LeanElement {
        return self::createTreeFromDOMElement($domDocument->documentElement);
    }
    
    /**
     * @param DOMElement $domElement
     * @return LeanElement
     */
    public static function createTreeFromDOMElement(DOMElement $domElement): LeanElement {
        return self::createOneFromDOMElement($domElement, self::createTreeListFromDOMNodeList($domElement->childNodes));
    }
    
    /**
     * @param DOMElement $element
     * @param array $children
     * @return LeanElement
     */
    public static function createOneFromDOMElement(DOMElement $element, array $children = []): LeanElement {
        $tag = $element->localName;
        $attributes = [];
        foreach ($element->attributes as $attr) {
            $attributes[$attr->name] = $attr->value;
        }
        return self::createOneFromArray($tag, $attributes, $children);
    }
    
    /**
     * @param string $tag
     * @param array $attributes
     * @param iterable $children
     * @return LeanElement
     */
    public static function createOneFromArray(string $tag, array $attributes, iterable $children = []): LeanElement {
        return new LeanElement($tag, $attributes, $children instanceof Vector ? $children : new Vector($children));
    }
    
    private string $tag;
    
    private array $attributes;
    
    private Vector $children;
    
    /**
     *
     * @param string $$tag
     * @param array $attributes
     * @param array $children
     */
    private function __construct(string $tag, array $attributes, Vector $children) {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }
    
    /**
     *
     * @return string
     */
    public function getTag(): string {
        return $this->tag;
    }
    
    /**
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute(string $key): bool {
        return isset($this->attributes[$key]);
    }
    
    /**
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $key, $default = null) {
        return $this->attributes[$key] ?? $default;
    }
    
    /**
     *
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function setAttribute(string $key, $val): void {
        $this->attributes[$key] = $val;
    }
    
    /**
     *
     * @return array
     */
    public function getAttributes(): array {
        return $this->attributes;
    }
    
    /**
     *
     * @param LeanElement $child
     * @return void
     */
    public function appendChild(LeanElement $child): void {
        $this->children[] = $child;
    }
    
    /**
     *
     * @return LeanElement[]
     */
    public function getChildren(): iterable {
        return $this->children;
    }
    
    /**
     * @param string $tag
     * @return ?LeanElement
     */
    public function getChildByTag(string $tag): ?LeanElement {
        foreach ($this->children as $child) {
            if ($child->getTag() === $tag) {
                return $child;
            }
        }
        return null;
    }
    
    /**
     * @param DOMDocument $targetDoc
     * @return DOMElement
     */
    public function toElement(DOMDocument $targetDoc): DOMElement {
        $element = $targetDoc->createElement($this->tag);
        foreach ($this->attributes as $key => $val) {
            $element->setAttribute($key, $val);
        }
        foreach ($this->children as $child) {
            $element->appendChild($child->toElement($targetDoc));
        }
        return $element;
    }
    
    /**
     * @param array $attributes
     * @return LeanElement
     */
    public function withAttributes(array $attributes): LeanElement {
        $ret = clone $this;
        foreach ($attributes as $key => $val) {
            $ret->setAttribute($key, $val);
        }
        return $ret;
    }
    
    /**
     * @return array
     */
    public function __serialize(): array {
        return [
            $this->tag,
            $this->attributes,
            $this->children->toArray()
        ];
    }
    
    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void {
        $this->tag = (string) $data[0] ?? '';
        $this->attributes = (array) $data[1] ?? [];
        $this->children = new Vector((array) $data[2] ?? []);
    }
}
