<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use DOMDocument;
use DOMElement;
use PHPUnit\Framework\TestCase;

/**
 * DOMWriterFromElementDelegateTest
 *
 * @see DOMWriterFromElementDelegate
 */
final class DOMWriterFromElementDelegateTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DOMWriterFromElementDelegate::class), "Failed to load class 'Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromElementDelegate'!");
    }
    
    public function testElementIsImportedIntoEachTargetDocument(): void {
        $source = new DOMDocument();
        $element = $source->createElement('root');
        $element->appendChild($source->createElement('child', 'value'));
        $calls = 0;
        $sut = new DOMWriterFromElementDelegate(function () use ($element, &$calls) {
            $calls++;
            return $element;
        });
        
        $this->assertSame($element, $sut->toElement($source));
        $target = new DOMDocument();
        $actual = $sut->toElement($target);
        $this->assertInstanceOf(DOMElement::class, $actual);
        $this->assertSame($target, $actual->ownerDocument);
        $this->assertSame('<root><child>value</child></root>', $target->saveXML($actual));
        $this->assertSame(1, $calls);
    }
    
}