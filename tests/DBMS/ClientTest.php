<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;

/**
 * ClientTest
 *
 * @see Client
 *
 * @todo auto-generated
 */
class ClientTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(Client::class), "Failed to load class 'Slothsoft\Core\DBMS\Client'!");
    }
}