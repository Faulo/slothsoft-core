<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Client::class));
    }
}