<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

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

        $sut = new CacheDirectoryStorage();

        $sut->install();

        $this->assertDirectoryExists($directory);
    }

    /**
     *
     * @dataProvider storageProvider
     * @test
     */
    public function given_store_when_retrieve_then_return(string $storeKey, string $retrieveKey, int $storeTime, int $retrieveTime, string $storeValue, ?string $retrieveValue): void {
        $sut = new CacheDirectoryStorage();
        $sut->install();

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
        $sut->install();

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
        $sut->install();

        $this->assertTrue($sut->storeXML($storeKey, $storeValue, $storeTime));

        $actual = $sut->retrieveXML($retrieveKey, $retrieveTime);

        if ($retrieveValue === null) {
            $this->assertNull($actual);
        } else {
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
        $sut->install();

        $this->assertTrue($sut->storeXML($storeKey, $storeValue, $storeTime));

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
}