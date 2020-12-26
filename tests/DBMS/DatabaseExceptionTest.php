<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;

class DatabaseExceptionTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(DatabaseException::class));
    }
}