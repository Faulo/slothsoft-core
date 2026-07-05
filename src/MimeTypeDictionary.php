<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use DOMDocument;

/**
 * MIME type, file extension, and compression lookup helper.
 *
 * @author Daniel Schulz
 * @since 2018-01-23
 */
final class MimeTypeDictionary {
    
    const FILE_MIME = __DIR__ . '/../mimeTypes.xml';
    
    private static bool $initialized = false;
    
    private static array $mimeExtensionList = [];
    
    private static array $mimeCompressionsList = [];
    
    private static array $extensionMimeList = [];
    
    private static function init(): void {
        if (! self::$initialized) {
            self::$initialized = true;
            
            $mimeDoc = new DOMDocument();
            $mimeDoc->load(self::FILE_MIME);
            foreach ($mimeDoc->getElementsByTagName('sub') as $subNode) {
                $typeNode = $subNode->parentNode;
                
                $mime = $typeNode->getAttribute('name') . '/' . $subNode->getAttribute('name');
                $extension = $subNode->getAttribute('ext');
                
                self::$mimeExtensionList[$mime] = $extension;
                self::$extensionMimeList[$extension] = $mime;
                
                if ($typeNode->hasAttribute('compressions')) {
                    self::$mimeCompressionsList[$mime] = $typeNode->getAttribute('compressions');
                }
                if ($subNode->hasAttribute('compressions')) {
                    self::$mimeCompressionsList[$mime] = $subNode->getAttribute('compressions');
                }
            }
        }
    }
    
    /**
     * @param string $mime
     * @return string
     */
    public static function guessExtension(string $mime): string {
        self::init();
        
        $mime = strtolower($mime);
        
        return self::$mimeExtensionList[$mime] ?? '';
    }
    
    /**
     * @param string $extension
     * @return string
     */
    public static function guessMime(string $extension): string {
        self::init();
        
        $extension = strtolower($extension);
        
        return self::$extensionMimeList[$extension] ?? 'application/octet-stream';
    }
    
    /**
     * @param string $mime
     * @return string
     */
    public static function guessCompressions(string $mime): string {
        self::init();
        
        $mime = strtolower($mime);
        
        return self::$mimeCompressionsList[$mime] ?? '';
    }
    
    /**
     * @param string $extension
     * @param string $testMime
     * @return bool
     */
    public static function matchesMime(string $extension, string $testMime): bool {
        if ($testMime === '*/*') {
            return true;
        }
        
        $extMime = self::guessMime($extension);
        
        $extMime = explode('/', $extMime, 2);
        $testMime = explode('/', $testMime, 2);
        
        return ($testMime[0] === $extMime[0] and ($testMime[1] === '*' or $testMime[1] === $extMime[1]));
    }
    
    /**
     * @param string $type
     * @return bool
     */
    public static function isXml(string $type): bool {
        return $type === 'application/xml' or substr($type, -4) === '+xml';
    }
    
    /**
     * @param string $type
     * @return bool
     */
    public static function isHtml(string $type): bool {
        return $type === 'text/html';
    }
    
    /**
     * @param string $type
     * @return bool
     */
    public static function isText(string $type): bool {
        return $type === 'application/javascript' or $type === 'application/json' or substr($type, 0, 5) === 'text/' or self::isXml($type);
    }
}
