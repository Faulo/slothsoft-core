<?php
declare(strict_types = 1);
namespace tests\Slothsoft\Core;

/**
 * Filesystem test case.
 */
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\FileSystem;
use DOMDocument;
use DOMElement;

class FileSystemTest extends TestCase
{
    /**
     * Tests Filesystem::classNameToFilename()
     */
    public function testAsNode()
    {
        $document = FileSystem::asNode(__DIR__);
        
        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertInstanceOf(DOMElement::class, $document->documentElement);
    }
}

