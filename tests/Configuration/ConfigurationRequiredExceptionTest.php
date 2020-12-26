<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Configuration;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @see ConfigurationRequiredException
 *
 * @todo auto-generated
 */
class ConfigurationRequiredExceptionTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(ConfigurationRequiredException::class), "Failed to load class 'Slothsoft\Core\Configuration\ConfigurationRequiredException'!");
    }
}