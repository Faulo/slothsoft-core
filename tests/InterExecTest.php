<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * InterExecTest
 *
 * @see InterExec
 */
final class InterExecTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(InterExec::class), "Failed to load class 'Slothsoft\Core\InterExec'!");
    }
    
    public function testInputBeforeOutputAndFractionalTickInterval(): void {
        $sut = new class('') extends InterExec {
            private int $checks = 0;
            
            public function is_running(): bool {
                return ++$this->checks < 3;
            }
            
            public function runLoop(): void {
                $this->run_mainloop();
            }
        };
        $listener = stream_socket_server('tcp://127.0.0.1:0');
        $this->assertIsResource($listener);
        $address = stream_socket_get_name($listener, false);
        $sut->pipes = [];
        $peers = [];
        for ($index = 0; $index < 3; $index++) {
            $sut->pipes[] = stream_socket_client('tcp://' . $address);
            $peers[] = stream_socket_accept($listener);
        }
        fclose($listener);
        $sut->time_start = microtime(true);
        $sut->tick_interval = 0.000001;
        $buffers = [];
        $sut->on('input', function (InterExec $process, string $buffer) use (&$buffers): string {
            $buffers[] = $buffer;
            return 'input';
        });
        try {
            $sut->runLoop();
            $this->assertSame([''], $buffers);
            $this->assertSame('', $sut->stdout);
        } finally {
            foreach (array_merge($sut->pipes, $peers) as $pipe) {
                fclose($pipe);
            }
        }
    }
    
}