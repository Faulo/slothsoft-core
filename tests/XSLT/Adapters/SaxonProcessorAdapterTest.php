<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use PHPUnit\Framework\TestCase;

class SaxonProcessorAdapterTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(SaxonProcessorAdapter::class));
    }
}