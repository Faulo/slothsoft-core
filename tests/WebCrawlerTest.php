<?php
declare(strict_types = 1);
namespace Slothsoft\Core;
        
use PHPUnit\Framework\TestCase;
        
/**
 * WebCrawlerTest
 *
 * @see WebCrawler
 *
 * @todo auto-generated
 */
class WebCrawlerTest extends TestCase {
        
    public function testClassExists(): void {
        $this->assertTrue(class_exists(WebCrawler::class), "Failed to load class 'Slothsoft\Core\WebCrawler'!");
    }
}