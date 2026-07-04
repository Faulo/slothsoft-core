<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use PHPUnit\Framework\TestCase;

/**
 * FileCacheTraitTest
 *
 * @see FileCacheTrait
 *
 * @todo auto-generated
 */
final class FileCacheTraitTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testTraitExists(): void {
        $this->assertTrue(trait_exists(FileCacheTrait::class), "Failed to load trait 'Slothsoft\Core\IO\Writable\Decorators\FileCacheTrait'!");
    }
}