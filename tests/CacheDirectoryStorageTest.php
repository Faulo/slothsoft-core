<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMNode;

class CacheDirectoryStorageTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(CacheDirectoryStorage::class), "Failed to load class 'Slothsoft\Core\CacheDirectoryStorage'!");
    }
    
    /**
     *
     * @test
     */
    public function when_install_then_createDirectory(): void {
        $directory = ServerEnvironment::getCacheDirectory() . DIRECTORY_SEPARATOR . 'storage';
        FileSystem::removeDir($directory, false);
        
        new CacheDirectoryStorage();
        
        $this->assertDirectoryExists($directory);
    }
    
    /**
     *
     * @test
     */
    public function when_install_with_name_then_createDirectory(): void {
        $directory = ServerEnvironment::getCacheDirectory() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'test';
        FileSystem::removeDir($directory, false);
        
        new CacheDirectoryStorage('test');
        
        $this->assertDirectoryExists($directory);
    }
    
    /**
     *
     * @test
     */
    public function given_store_when_delete_then_doStopExisting(): void {
        $storeKey = 'to-be-deleted';
        $storeValue = 'oh no';
        $storeTime = time();
        
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->store($storeKey, $storeValue, $storeTime));
        $this->assertTrue($sut->delete($storeKey, $storeValue, $storeTime));
        $this->assertFalse($sut->exists($storeKey, $storeTime));
        $this->assertNull($sut->retrieve($storeKey, $storeTime));
    }
    
    /**
     *
     * @dataProvider storageProvider
     * @test
     */
    public function given_store_when_retrieve_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->store($storeKey, $storeValue, $storeTime));
        
        $this->assertEquals($retrieveValue, $sut->retrieve($retrieveKey, $retrieveTime));
    }
    
    /**
     *
     * @dataProvider storageProvider
     * @test
     */
    public function given_store_when_exists_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->store($storeKey, $storeValue, $storeTime));
        
        if ($retrieveValue === null) {
            $this->assertFalse($sut->exists($retrieveKey, $retrieveTime));
        } else {
            $this->assertTrue($sut->exists($retrieveKey, $retrieveTime));
        }
    }
    
    public function storageProvider(): iterable {
        $now = time();
        
        yield 'same retrieve time works' => [
            'test',
            'test',
            $now,
            $now,
            'hello world',
            'hello world'
        ];
        
        yield 'older retrieve time works' => [
            'test',
            'test',
            1000,
            100,
            'hello world',
            'hello world'
        ];
        
        yield 'newer retrieve time is null' => [
            'test',
            'test',
            100,
            1000,
            'hello world',
            null
        ];
        
        yield 'unknown key is null' => [
            'test',
            'not-existing-key',
            $now,
            $now,
            'hello world',
            null
        ];
        
        yield 'can store empty string' => [
            'empty-string',
            'empty-string',
            $now,
            $now,
            '',
            ''
        ];
    }
    
    /**
     *
     * @dataProvider xmlProvider
     * @test
     */
    public function given_storeXML_when_retrieveXML_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $dom = new DOMHelper();
        $storeValue = $dom->parse($storeValue);
        
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->storeXML($storeKey, $storeValue, $storeTime));
        
        $actual = $sut->retrieveXML($retrieveKey, $retrieveTime);
        
        if ($retrieveValue === null) {
            $this->assertNull($actual);
        } else {
            $this->assertInstanceOf(DOMNode::class, $actual);
            $this->assertEquals($retrieveValue, $dom->stringify($actual));
        }
    }
    
    /**
     *
     * @dataProvider xmlProvider
     * @test
     */
    public function given_storeXML_when_exists_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $dom = new DOMHelper();
        $storeValue = $dom->parse($storeValue);
        
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->storeXML($storeKey, $storeValue, $storeTime));
        
        if ($retrieveValue === null) {
            $this->assertFalse($sut->exists($retrieveKey, $retrieveTime));
        } else {
            $this->assertTrue($sut->exists($retrieveKey, $retrieveTime));
        }
    }
    
    /**
     *
     * @dataProvider xmlProvider
     * @test
     */
    public function given_storeDocument_when_retrieveDocument_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $document = new DOMDocument();
        $document->loadXML($storeValue);
        $storeValue = $document;
        
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->storeDocument($storeKey, $storeValue, $storeTime));
        
        $actual = $sut->retrieveDocument($retrieveKey, $retrieveTime);
        
        if ($retrieveValue === null) {
            $this->assertNull($actual);
        } else {
            $this->assertInstanceOf(DOMDocument::class, $actual);
            
            $document = new DOMDocument();
            $document->loadXML($retrieveValue);
            $retrieveValue = $document->saveXML();
            $this->assertEquals($retrieveValue, $actual->saveXML());
        }
    }
    
    /**
     *
     * @dataProvider xmlProvider
     * @test
     */
    public function given_storeDocument_when_exists_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $document = new DOMDocument();
        $document->loadXML($storeValue);
        $storeValue = $document;
        
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->storeDocument($storeKey, $storeValue, $storeTime));
        
        if ($retrieveValue === null) {
            $this->assertFalse($sut->exists($retrieveKey, $retrieveTime));
        } else {
            $this->assertTrue($sut->exists($retrieveKey, $retrieveTime));
        }
    }
    
    public function xmlProvider(): iterable {
        $now = time();
        $xml = '<hello>world</hello>';
        
        yield 'same retrieve time works' => [
            'test',
            'test',
            $now,
            $now,
            $xml,
            $xml
        ];
        
        yield 'older retrieve time works' => [
            'test',
            'test',
            1000,
            100,
            $xml,
            $xml
        ];
        
        yield 'newer retrieve time is null' => [
            'test',
            'test',
            100,
            1000,
            $xml,
            null
        ];
        
        yield 'unknown key is null' => [
            'test',
            'not-existing-key',
            $now,
            $now,
            $xml,
            null
        ];
    }
    
    /**
     *
     * @dataProvider jsonProvider
     * @test
     */
    public function given_storeJSON_when_retrieveJSON_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, $storeValue, $retrieveValue): void {
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->storeJSON($storeKey, $storeValue, $storeTime));
        
        $actual = $sut->retrieveJSON($retrieveKey, $retrieveTime);
        
        if ($retrieveValue === null) {
            $this->assertNull($actual);
        } else {
            $this->assertJsonStringEqualsJsonString(json_encode($retrieveValue), json_encode($actual));
        }
    }
    
    /**
     *
     * @dataProvider jsonProvider
     * @test
     */
    public function given_storeJSON_when_exists_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, $storeValue, $retrieveValue): void {
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->storeJSON($storeKey, $storeValue, $storeTime));
        
        if ($retrieveValue === null) {
            $this->assertFalse($sut->exists($retrieveKey, $retrieveTime));
        } else {
            $this->assertTrue($sut->exists($retrieveKey, $retrieveTime));
        }
    }
    
    /**
     *
     * @test
     */
    public function given_retrieveJSON_when_invalidJSON_then_delete(): void {
        $storeKey = 'invalid-json';
        $storeTime = time();
        
        $sut = new CacheDirectoryStorage();
        
        $this->assertTrue($sut->store($storeKey, 'invalid json!?', $storeTime));
        $this->assertNull($sut->retrieveJSON($storeKey, $storeTime));
        $this->assertFalse($sut->exists($storeKey, $storeTime));
    }
    
    public function jsonProvider(): iterable {
        $now = time();
        $json = [
            'hello' => 'world',
            'a' => [
                [],
                0,
                'abc'
            ]
        ];
        
        yield 'same retrieve time works' => [
            'test',
            'test',
            $now,
            $now,
            $json,
            $json
        ];
        
        yield 'older retrieve time works' => [
            'test',
            'test',
            1000,
            100,
            $json,
            $json
        ];
        
        yield 'newer retrieve time is null' => [
            'test',
            'test',
            100,
            1000,
            $json,
            null
        ];
        
        yield 'unknown key is null' => [
            'test',
            'not-existing-key',
            $now,
            $now,
            $json,
            null
        ];
    }
}