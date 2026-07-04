<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use PHPUnit\Framework\TestCase;

/**
 * CliAdapterTest
 *
 * @see CliAdapter
 */
final class CliAdapterTest extends TestCase {

    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(CliAdapter::class), "Failed to load class 'Slothsoft\Core\XSLT\Adapters\CliAdapter'!");
    }

    /**
     *
     * @test
     */
    public function classImplementsAdapterInterface(): void {
        $this->assertContains(AdapterInterface::class, class_implements(CliAdapter::class));
    }
}
