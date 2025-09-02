<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * StorageTest
 *
 * @see Storage
 */
class StorageTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Storage::class), "Failed to load class 'Slothsoft\Core\Storage'!");
    }

    public function testExists(): void {
        if (! extension_loaded('mysqli')) {
            $this->markTestSkipped('Storage requires the mysqli extension.');
            return;
        }

        $sut = new Storage('not-existing-storage');

        $actual = $sut->exists('not-existing-key', 0);

        $this->assertEquals(false, $actual);
    }
}