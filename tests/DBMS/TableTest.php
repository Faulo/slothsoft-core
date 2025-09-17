<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;

/**
 * TableTest
 *
 * @see Table
 *
 * @todo auto-generated
 */
class TableTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Table::class), "Failed to load class 'Slothsoft\Core\DBMS\Table'!");
    }
}