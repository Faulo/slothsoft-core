<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Calendar;

use PHPUnit\Framework\TestCase;

class DateTimeFormatterTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(DateTimeFormatter::class));
    }
}