<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;

class FileSystemTest extends TestCase {
    
    public function testAsNode() {
        $document = FileSystem::asNode(__DIR__);
        
        $this->assertInstanceOf(DOMDocument::class, $document);
        $this->assertInstanceOf(DOMElement::class, $document->documentElement);
    }
    
    public function testRemoveDirIncludingRoot() {
        $directory = temp_file(__NAMESPACE__);
        
        mkdir("$directory/A", 0777, true);
        file_put_contents("$directory/A/B", 'test');
        
        $this->assertFileExists("$directory/A/B");
        
        // FileSystem::removeDir($directory, false);
        
        $this->assertFileNotExists("$directory/A/B");
        $this->assertDirectoryNotExists("$directory/A");
        $this->assertDirectoryNotExists($directory);
    }
    
    public function testRemoveDirExcludingRoot() {
        $directory = temp_file(__NAMESPACE__);
        
        mkdir("$directory/A", 0777, true);
        file_put_contents("$directory/A/B", 'test');
        
        $this->assertFileExists("$directory/A/B");
        
        // FileSystem::removeDir($directory, true);
        
        $this->assertFileNotExists("$directory/A/B");
        $this->assertDirectoryNotExists("$directory/A");
        $this->assertDirectoryExists($directory);
    }
}

