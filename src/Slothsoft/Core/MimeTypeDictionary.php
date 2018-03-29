<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use DOMDocument;
use DOMXPath;

/**
 *
 * @author Daniel Schulz
 *        
 */
class MimeTypeDictionary
{

    const FILE_MIME = __DIR__ . '/../../../mimeTypes.xml';

    private static $initialized = false;

    private static $mimeExtensionList;

    private static $extensionMimeList;

    private static function init()
    {
        if (! self::$initialized) {
            self::$initialized = true;
            
            self::$mimeExtensionList = [];
            self::$extensionMimeList = [];
            
            $mimeDoc = new DOMDocument();
            $mimeDoc->load(self::FILE_MIME);
            foreach ($mimeDoc->getElementsByTagName('sub') as $subNode) {
                $typeNode = $subNode->parentNode;
                
                $mime = $typeNode->getAttribute('name') . '/' . $subNode->getAttribute('name');
                $extension = $subNode->getAttribute('ext');
                
                self::$mimeExtensionList[$mime] = $extension;
                self::$extensionMimeList[$extension] = $mime;
            }
        }
    }

    protected static function getMimePath()
    {
        if (! self::$mimeList) {
            
            self::$mimePath = new DOMXPath($mimeDoc);
        }
        return self::$mimeList;
    }

    public static function guessExtension(string $mime): string
    {
        self::init();
        
        $mime = strtolower($mime);
        
        return isset(self::$mimeExtensionList[$mime]) ? self::$mimeExtensionList[$mime] : '';
    }

    public static function guessMime(string $extension): string
    {
        self::init();
        
        $extension = strtolower($extension);
        
        return isset(self::$extensionMimeList[$extension]) ? self::$extensionMimeList[$extension] : 'application/octet-stream';
    }

    public static function matchesMime(string $extension, string $testMime): bool
    {
        if ($testMime === '*/*') {
            return true;
        }
        
        $extMime = self::guessMime($extension);
        
        $extMime = explode('/', $extMime, 2);
        $testMime = explode('/', $testMime, 2);
        
        return ($testMime[0] === $extMime[0] and ($testMime[1] === '*' or $testMime[1] === $extMime[1]));
    }
}

