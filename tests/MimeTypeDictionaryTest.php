<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class MimeTypeDictionaryTest extends TestCase {
    
    public function someMimeTypes(): array {
        return [
            [
                'html',
                'text/html',
                'text/*',
                false
            ],
            [
                'xhtml',
                'application/xhtml+xml',
                'application/*',
                true
            ],
            [
                'svg',
                'image/svg+xml',
                'image/*',
                true
            ],
            [
                'xml',
                'application/xml',
                'application/*',
                true
            ],
            [
                'bin',
                'application/octet-stream',
                'application/*',
                false
            ]
        ];
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testGuessExtensions(string $extension, string $mimeType, string $parentMimeType, bool $isXml) {
        $this->assertEquals($extension, MimeTypeDictionary::guessExtension($mimeType));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testGuessMimeType(string $extension, string $mimeType, string $parentMimeType, bool $isXml) {
        $this->assertEquals($mimeType, MimeTypeDictionary::guessMime($extension));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testMatchesMime(string $extension, string $mimeType, string $parentMimeType, bool $isXml) {
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, $mimeType));
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, $parentMimeType));
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, '*/*'));
    }
    
    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testIsXml(string $extension, string $mimeType, string $parentMimeType, bool $isXml) {
        $this->assertEquals($isXml, MimeTypeDictionary::isXml($mimeType));
    }
}