<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;
        
use PHPUnit\Framework\TestCase;
        
/**
 * DatabaseExceptionTest
 *
 * @see DatabaseException
 *
 * @todo auto-generated
 */
class DatabaseExceptionTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DatabaseException::class), "Failed to load class 'Slothsoft\Core\DBMS\DatabaseException'!");
    }
}