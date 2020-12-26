<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;
        
use PHPUnit\Framework\TestCase;
        
/**
 * ManagerTest
 *
 * @see Manager
 *
 * @todo auto-generated
 */
class ManagerTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Manager::class), "Failed to load class 'Slothsoft\Core\DBMS\Manager'!");
    }
}