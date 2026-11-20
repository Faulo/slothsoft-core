<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class FileSystemDownloadTest extends TestCase {
    
    /** @throws Exception */
    public function testDownloadResponses(): void {
        $directory = temp_dir(__NAMESPACE__);
        try {
            file_put_contents($directory . '/data.bin', 'abcdef');
            file_put_contents($directory . '/empty.bin', '');
            
            $socket = stream_socket_server('tcp://127.0.0.1:0');
            $this->assertIsResource($socket);
            $address = stream_socket_get_name($socket, false);
            fclose($socket);
            $this->assertIsString($address);
            $server = new Process([PHP_BINARY, '-S', $address, __DIR__ . '/../test-files/FileSystem/download.php'], $directory);
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
                
                $url = 'http://' . $address;
                $this->assertDownload($url, '', 200, 'abcdef', null);
                $this->assertDownload($url, 'bytes=2-4', 206, 'cde', 'bytes 2-4/6');
                $this->assertDownload($url, 'bytes=0-', 206, 'abcdef', 'bytes 0-5/6');
                $this->assertDownload($url, 'bytes=-2', 206, 'ef', 'bytes 4-5/6');
                $this->assertDownload($url, 'bytes=00005-999999999999999999999', 206, 'f', 'bytes 5-5/6');
                $this->assertDownload($url, 'bytes=-999999999999999999999', 206, 'abcdef', 'bytes 0-5/6');
                $this->assertDownload($url, 'bytes=6-', 416, '', 'bytes */6');
                $this->assertDownload($url, 'bytes=999999999999999999999-', 416, '', 'bytes */6');
                $this->assertDownload($url, 'bytes=4-2', 416, '', 'bytes */6');
                $this->assertDownload($url, 'bytes=-0', 416, '', 'bytes */6');
                $this->assertDownload($url, 'bytes=0-1,3-4', 200, 'abcdef', null);
                $this->assertDownload($url . '/empty', '', 200, '', null);
                $this->assertDownload($url . '/empty', 'bytes=0-', 416, '', 'bytes */0');
                $this->assertSame([], glob($directory . '/ENDBYTE.*.txt'));
            } finally {
                $server->stop();
            }
        } finally {
            FileSystem::removeDir($directory);
        }
    }
    
    private function assertDownload(string $url, string $range, int $status, string $body, ?string $contentRange): void {
        $client = curl_init($url);
        $this->assertNotFalse($client);
        curl_setopt_array($client, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_NOPROXY => '*',
            CURLOPT_HTTPHEADER => $range === '' ? [] : ["Range: $range"]
        ]);
        $response = curl_exec($client);
        $this->assertIsString($response, curl_error($client));
        $headerSize = curl_getinfo($client, CURLINFO_HEADER_SIZE);
        $this->assertSame($status, curl_getinfo($client, CURLINFO_RESPONSE_CODE), $range);
        $this->assertSame($body, substr($response, $headerSize), $range);
        $headers = explode("\r\n", strtolower(substr($response, 0, $headerSize)));
        $this->assertContains('content-length: ' . strlen($body), $headers, $range);
        $this->assertNotContains('transfer-encoding: chunked', $headers, $range);
        $rangeHeaders = array_values(array_filter($headers, static function (string $header): bool {
            return strpos($header, 'content-range:') === 0;
        }));
        $this->assertSame($contentRange === null ? [] : ["content-range: $contentRange"], $rangeHeaders, $range);
    }
}
