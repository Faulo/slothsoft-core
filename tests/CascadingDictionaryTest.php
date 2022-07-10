<?php
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class CascadingDictionaryTest extends TestCase {

    /**
     *
     * @test
     */
    public function testGetIterator() {
        $dict = new CascadingDictionary();

        $dict['A'] = 1;
        $dict['B'] = 2;

        $this->assertEquals([
            'A' => 1,
            'B' => 2
        ], iterator_to_array($dict->getIterator()));
    }

    /**
     *
     * @test
     */
    public function testIsset() {
        $dict = new CascadingDictionary();

        $dict['A'] = 1;

        $this->assertTrue(isset($dict['A']));
        $this->assertFalse(isset($dict['B']));
    }

    /**
     *
     * @test
     */
    public function testUnset() {
        $dict = new CascadingDictionary();

        $dict['A'] = 1;

        $this->assertTrue(isset($dict['A']));

        unset($dict['A']);

        $this->assertFalse(isset($dict['A']));
    }

    /**
     *
     * @test
     */
    public function testCascadingIsset() {
        $dict = new CascadingDictionary();

        $dict['A/B'] = 1;

        $this->assertTrue(isset($dict['A/B/C']));

        $this->assertTrue(isset($dict['A/B']));

        $this->assertFalse(isset($dict['A']));
    }

    /**
     *
     * @test
     */
    public function testCascadingValue() {
        $dict = new CascadingDictionary();

        $dict['A'] = 1;

        $dict['A/B/C'] = 3;

        $dict['A/B'] = 2;

        $this->assertEquals(3, $dict['A/B/C']);

        $this->assertEquals(3, $dict['A/B/C/X']);

        $this->assertEquals(2, $dict['A/B']);

        $this->assertEquals(2, $dict['A/B/X']);

        $this->assertEquals(1, $dict['A']);

        $this->assertEquals(1, $dict['A/X']);
    }

    /**
     *
     * @test
     */
    public function testCascadingGetIterator() {
        $dict = new CascadingDictionary();

        $dict['A'] = 1;

        $dict['A/B/C'] = 3;

        $dict['A/B'] = 2;

        $expected = [
            'A/B/C' => 3,
            'A/B' => 2,
            'A' => 1
        ];
        $expectedKeys = array_keys($expected);
        $expectedValues = array_values($expected);

        $i = 0;
        foreach ($dict as $key => $value) {
            $this->assertEquals($expectedKeys[$i], $key);
            $this->assertEquals($expectedValues[$i], $value);
            $i ++;
        }
        $this->assertEquals(3, $i);
    }
}

