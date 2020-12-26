<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Database::class));
    }
}