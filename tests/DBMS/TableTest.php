<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;

class TableTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Table::class));
    }
}