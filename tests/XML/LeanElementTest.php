<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XML;

use PHPUnit\Framework\TestCase;

/**
 * LeanElementTest
 *
 * @see LeanElement
 *
 * @todo auto-generated
 */
class LeanElementTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(LeanElement::class), "Failed to load class 'Slothsoft\Core\XML\LeanElement'!");
    }

    public function testThatTagGetsSerialized(): void {
        $tag = 'test';

        $sut = LeanElement::createOneFromArray($tag, [], []);

        $actual = unserialize(serialize($sut));

        $this->assertEquals($tag, $actual->getTag());
    }

    public function testThatAttributeGetsSerialized(): void {
        $key = 'hello';
        $value = 'world';

        $sut = LeanElement::createOneFromArray('tag', [
            $key => $value
        ], []);

        $actual = unserialize(serialize($sut));

        $this->assertEquals($value, $actual->getAttribute($key));
    }

    public function testThatChildGetsSerialized(): void {
        $tag = 'child';
        $key = 'hello';
        $value = 'world';

        $child = LeanElement::createOneFromArray($tag, [
            $key => $value
        ], []);

        $sut = LeanElement::createOneFromArray('tag', [], [
            $child
        ]);

        $actual = unserialize(serialize($sut));

        $this->assertEquals($value, $actual->getChildByTag($tag)
            ->getAttribute($key));
    }

    public function testGetChildByTag(): void {
        $tag = 'child';
        $key = 'hello';
        $value = 'world';

        $child = LeanElement::createOneFromArray($tag, [
            $key => $value
        ], []);

        $sut = LeanElement::createOneFromArray('tag', [], [
            $child
        ]);

        $this->assertEquals($value, $sut->getChildByTag($tag)
            ->getAttribute($key));
    }
}