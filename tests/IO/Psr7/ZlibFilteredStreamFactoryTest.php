<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;

/**
 * ZlibFilteredStreamFactoryTest
 *
 * @see ZlibFilteredStreamFactory
 *
 * @todo auto-generated
 */
class ZlibFilteredStreamFactoryTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(ZlibFilteredStreamFactory::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\ZlibFilteredStreamFactory'!");
    }
}