<?php
declare(strict_types = 1);
namespace Slothsoft\Core;
        
use PHPUnit\Framework\TestCase;
        
/**
 * StorageTest
 *
 * @see Storage
 *
 * @todo auto-generated
 */
class StorageTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Storage::class), "Failed to load class 'Slothsoft\Core\Storage'!");
    }
}