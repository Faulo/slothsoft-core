<?php
namespace tests\Slothsoft\Core;

/**
 * Filesystem test case.
 */
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\FileSystem;
use DOMDocument;

class FileSystemTest extends TestCase
{
    /**
     * Tests Filesystem::classNameToFilename()
     */
    public function testAsNode()
    {
        $document = FileSystem::asNode(__DIR__);
        
        assert($document instanceof DOMDocument);
    }
}

