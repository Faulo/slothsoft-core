<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

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
    public function writeDocumentReturnsDocumentLoadedFromOutputFile(): void {
        $sut = new class() extends CliAdapter {

            public function __construct() {
                parent::__construct('', '');
            }

            public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo {
                return $outputFile ?? FileInfoFactory::createFromString('<result>ok</result>');
            }
        };

        $document = $sut->writeDocument();

        $this->assertSame('result', $document->documentElement->tagName);
        $this->assertSame('ok', $document->documentElement->textContent);
    }
}
