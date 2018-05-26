<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMElement;

class DOMHelperTest extends TestCase
{

    private $dom;

    public function setUp() : void
    {
        $this->dom = new DOMHelper();
    }

    public function testIsThereAnySyntaxError()
    {
        $xml = '<xml/>';
        $fragment = $this->dom->parse($xml);
        
        $this->assertInstanceOf(DOMElement::class, $fragment->firstChild);
        $this->assertEquals('xml', $fragment->firstChild->tagName);
    }
}