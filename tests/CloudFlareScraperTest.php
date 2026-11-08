<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * CloudFlareScraperTest
 *
 * @see CloudFlareScraper
 *
 */
final class CloudFlareScraperTest extends TestCase {
    /** @runInSeparateProcess */
    public function testUnsolvedChallengeReturnsNull(): void {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $this->assertIsResource($socket);
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        $server = new \Symfony\Component\Process\Process([
            PHP_BINARY, '-S', $address, __DIR__ . '/../test-files/CloudFlareScraper/unsolved.php'
        ]);
        $server->start();
        try {
            $ready = false;
            for ($attempt = 0; $attempt < 100; $attempt++) {
                $connection = @stream_socket_client('tcp://' . $address);
                if ($connection !== false) {
                    fclose($connection);
                    $ready = true;
                    break;
                }
                usleep(10000);
            }
            $this->assertTrue($ready, 'Local HTTP server did not start: ' . $server->getErrorOutput());
            $uri = 'http://' . $address;
            $this->assertNull((new CloudFlareScraper())->getFile($uri));
            $this->assertNull((new CloudFlareScraper())->getXPath($uri));
        } finally {
            $server->stop();
        }
    }
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(CloudFlareScraper::class), "Failed to load class 'Slothsoft\Core\CloudFlareScraper'!");
    }
}
