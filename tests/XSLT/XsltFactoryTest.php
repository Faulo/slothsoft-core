<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT;

use PHPUnit\Framework\TestCase;

/**
 * XsltFactoryTest
 *
 * @see XsltFactory
 *
 * @todo auto-generated
 */
final class XsltFactoryTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(XsltFactory::class), "Failed to load class 'Slothsoft\Core\XSLT\XsltFactory'!");
    }
}