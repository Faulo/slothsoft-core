<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class MimeTypeDictionaryTest extends TestCase {
    
    public function someMimeTypes(): array {
        return [
            [
                'txt',
                'text/plain',
                'text/*',
                false,
                false,
                true
            ],
            [
                'html',
                'text/html',
                'text/*',
                false,
                true,
                true
            ],
            [
                'xhtml',
                'application/xhtml+xml',
                'application/*',
                true,
                false,
                true
            ],
            [
                'svg',
                'image/svg+xml',
                'image/*',
                true,
                false,
                true
            ],
            [
                'xml',
                'application/xml',
                'application/*',
                true,
                false,
                true
            ],
            [
                'bin',
                'application/octet-stream',
                'application/*',
                false,
                false,
                false
            ],
            [
                'js',
                'application/javascript',
                'application/*',
                false,
                false,
                true
            ],
            [
                'json',
                'application/json',
                'application/*',
                false,
                false,
                true
            ]
        ];
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testGuessExtensions(string $extension, string $mimeType, string $parentMimeType, bool $isXml, $isHtml, $isText) {
        $this->assertEquals($extension, MimeTypeDictionary::guessExtension($mimeType));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testGuessMimeType(string $extension, string $mimeType, string $parentMimeType, bool $isXml, $isHtml, $isText) {
        $this->assertEquals($mimeType, MimeTypeDictionary::guessMime($extension));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testMatchesMime(string $extension, string $mimeType, string $parentMimeType, bool $isXml, $isHtml, $isText) {
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, $mimeType));
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, $parentMimeType));
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, '*/*'));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testIsXml(string $extension, string $mimeType, string $parentMimeType, bool $isXml, $isHtml, $isText) {
        $this->assertEquals($isXml, MimeTypeDictionary::isXml($mimeType));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testIsHtml(string $extension, string $mimeType, string $parentMimeType, bool $isXml, $isHtml, $isText) {
        $this->assertEquals($isHtml, MimeTypeDictionary::isHtml($mimeType));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testIsText(string $extension, string $mimeType, string $parentMimeType, bool $isXml, $isHtml, $isText) {
        $this->assertEquals($isText, MimeTypeDictionary::isText($mimeType));
    }
}