<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;
        
use PHPUnit\Framework\TestCase;
        
/**
 * @todo auto-generated
 */
class DatabaseTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Database::class), "Failed to load class 'Slothsoft\Core\DBMS\Database'!");
    }
}