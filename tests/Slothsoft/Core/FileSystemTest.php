<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;

class FileSystemTest extends TestCase
{
    public function testAsNode()
    {
        $document = FileSystem::asNode(__DIR__);
        
        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertInstanceOf(DOMElement::class, $document->documentElement);
    }
}

