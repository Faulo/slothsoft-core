<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;
        
use PHPUnit\Framework\TestCase;
        
/**
 * AuthorityTest
 *
 * @see Authority
 *
 * @todo auto-generated
 */
class AuthorityTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Authority::class), "Failed to load class 'Slothsoft\Core\DBMS\Authority'!");
    }
}