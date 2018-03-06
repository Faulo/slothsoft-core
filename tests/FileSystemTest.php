<?php

/**
 * Filesystem test case.
 */
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\FileSystem;

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

