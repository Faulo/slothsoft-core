<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XML;

use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use DOMDocument;
use DOMElement;
use DOMNodeList;

/**
 *
 * @author Daniel Schulz
 *        
 */
class LeanElement implements DOMWriterInterface
{
    use DOMWriterDocumentFromElementTrait;

    public static function createTreeListFromDOMNodeList(DOMNodeList $domNodeList): array
    {
        $ret = [];
        foreach ($domNodeList as $domNode) {
            if ($domNode instanceof DOMElement) {
                $ret[] = self::createTreeFromDOMElement($domNode);
            }
        }
        return $ret;
    }

    public static function createTreeFromDOMDocument(DOMDocument $domDocument): LeanElement
    {
        return self::createTreeFromDOMElement($domDocument->documentElement);
    }

    public static function createTreeFromDOMElement(DOMElement $domElement): LeanElement
    {
        return self::createOneFromDOMElement($domElement, self::createTreeListFromDOMNodeList($domElement->childNodes));
    }

    public static function createOneFromDOMElement(DOMElement $element, array $children = []): LeanElement
    {
        $tag = $element->localName;
        $attributes = [];
        foreach ($element->attributes as $attr) {
            $attributes[$attr->name] = $attr->value;
        }
        return self::createOneFromArray($tag, $attributes, $children);
    }

    public static function createOneFromArray(string $tag, array $attributes, array $children = []): LeanElement
    {
        return new LeanElement($tag, $attributes, $children);
    }

    private $tag;

    private $attributes;

    private $children;

    /**
     *
     * @param string $$tag
     * @param array $attributes
     * @param array $children
     */
    private function __construct(string $tag, array $attributes, array $children)
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    /**
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key, $default = null): string
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     *
     * @param string $key
     * @param mixed $val
     */
    public function setAttribute(string $key, string $val)
    {
        $this->attributes[$key] = $val;
    }

    /**
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     *
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChildByTag(string $tag)
    {
        foreach ($this->children as $child) {
            if ($child->getTag() === $tag) {
                return $child;
            }
        }
    }

    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        $element = $targetDoc->createElement($this->tag);
        foreach ($this->attributes as $key => $val) {
            $element->setAttribute($key, $val);
        }
        foreach ($this->children as $child) {
            $element->appendChild($child->toElement($targetDoc));
        }
        return $element;
    }

    public function withAttributes(array $attributes): LeanElement
    {
        $ret = clone $this;
        foreach ($attributes as $key => $val) {
            $ret->setAttribute($key, $val);
        }
        return $ret;
    }
}

