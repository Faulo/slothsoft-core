<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;

final class XMLHttpRequestTest extends TestCase {
    
    /**
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(XMLHttpRequest::class), "Failed to load class 'Slothsoft\Core\XMLHttpRequest'!");
    }
    
    /**
     *
     * @test
     */
    
    public function testConstructor() {
        $sut = new XMLHttpRequest();
        
        $this->assertThat($sut->readyState, new IsEqual(XMLHttpRequest::UNSENT));
    }
    
    /**
     * @test
     */
    public function testDownload() {
        $sut = new XMLHttpRequest();
        $sut->open('GET', 'https://www.w3.org', false);
        
        $this->assertThat($sut->readyState, new IsEqual(XMLHttpRequest::OPENED));
    }
}