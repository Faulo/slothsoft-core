<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see HTTPStream
 *
 * @todo auto-generated
 */
class HTTPStreamTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(HTTPStream::class), "Failed to load class 'Slothsoft\Core\IO\HTTPStream'!");
    }
}