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
                'text/*'
            ],
            [
                'xhtml',
                'application/xhtml+xml',
                'application/*'
            ],
            [
                'svg',
                'image/svg+xml',
                'image/*'
            ]
        ];
    }

    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testGuessExtensions(string $extension, string $mimeType, string $parentMimeType) {
        $this->assertEquals($extension, MimeTypeDictionary::guessExtension($mimeType));
    }

    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testGuessMimeType(string $extension, string $mimeType, string $parentMimeType) {
        $this->assertEquals($mimeType, MimeTypeDictionary::guessMime($extension));
    }

    /**
     *
     * @dataProvider someMimeTypes
     */
    public function testMatchesMime(string $extension, string $mimeType, string $parentMimeType) {
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, $mimeType));
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, $parentMimeType));
        $this->assertTrue(MimeTypeDictionary::matchesMime($extension, '*/*'));
    }
}