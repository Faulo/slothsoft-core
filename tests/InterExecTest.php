<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * InterExecTest
 *
 * @see InterExec
 *
 * @todo auto-generated
 */
class InterExecTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(InterExec::class), "Failed to load class 'Slothsoft\Core\InterExec'!");
    }
}