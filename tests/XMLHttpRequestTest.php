<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;
use Throwable;
use Symfony\Component\Process\Process;

final class XMLHttpRequestTest extends TestCase {
    
    /**
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(XMLHttpRequest::class), "Failed to load class 'Slothsoft\Core\XMLHttpRequest'!");
    }
    
    /**
     *
     * @test
     */
    
    public function whenConstructed_thenStateIsUnsent() {
        $sut = new XMLHttpRequest();
        
        $this->assertThat($sut->readyState, new IsEqual(XMLHttpRequest::UNSENT));
    }
    
    /**
     * @test
     */
    public function whenOpened_thenStateIsOpened() {
        $sut = new XMLHttpRequest();
        $sut->open('GET', 'https://www.w3.org', false);
        
        $this->assertThat($sut->readyState, new IsEqual(XMLHttpRequest::OPENED));
    }
    
    /**
     * @test
     */
    public function whenSent_thenStateIsDone() {
        $sut = new XMLHttpRequest();
        $sut->open('GET', 'https://www.w3.org', false);
        try {
            $sut->send();
        } catch (Throwable $e) {
            $this->fail((string) $e);
        }
        
        $this->assertThat($sut->readyState, new IsEqual(XMLHttpRequest::DONE));
    }
    
    public function testSendWithRequestHeaders(): void {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $this->assertIsResource($socket);
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        $server = new Process([PHP_BINARY, '-S', $address, __DIR__ . '/../test-files/XMLHttpRequest/echo-header.php']);
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
            $sut = new XMLHttpRequest();
            $sut->open('GET', 'http://' . $address, false);
            $sut->setRequestHeader('X-Test', 'value');
            $sut->send();
            $this->assertSame('value', $sut->responseText);
            $this->assertSame(XMLHttpRequest::DONE, $sut->readyState);
        } finally {
            $server->stop();
        }
    }
    
}