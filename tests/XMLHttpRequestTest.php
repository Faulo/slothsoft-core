<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class XMLHttpRequestTest extends TestCase {
    public function testContructor() {
        $request = new XMLHttpRequest();
        $this->assertEquals(XMLHttpRequest::UNSENT, $request->readyState);
    }
}