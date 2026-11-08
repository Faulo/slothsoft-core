<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class CLITest extends TestCase {
    
    public function testDefaultTotalTimeout(): void {
        $this->assertEquals(0, CLI::getTotalTimeout());
    }
    
    public function testSetTotalTimeout(): void {
        CLI::setTotalTimeout(42);
        $this->assertEquals(42, CLI::getTotalTimeout());
    }
    
    public function testDefaultIdleTimeout(): void {
        $this->assertEquals(0, CLI::getIdleTimeout());
    }
    
    public function testSetIdleTimeout(): void {
        CLI::setIdleTimeout(42);
        $this->assertEquals(42, CLI::getIdleTimeout());
    }
    
    public function testDefaultStdOut(): void {
        $this->assertEquals(STDOUT, CLI::getStdOut());
    }
    
    public function testDefaultStdErr(): void {
        $this->assertEquals(STDERR, CLI::getStdErr());
    }
    
    public function testSetStdOut(): void {
        $handle = tmpfile();
        CLI::setStdOut($handle);
        $this->assertEquals($handle, CLI::getStdOut());
    }
    
    public function testSetStdErr(): void {
        $handle = tmpfile();
        CLI::setStdErr($handle);
        $this->assertEquals($handle, CLI::getStdErr());
    }
    
    public function testBasicExecute(): void {
        $command = PHP_BINARY . ' --version';
        $out = tmpfile();
        $err = tmpfile();
        CLI::setStdOut($out);
        CLI::setStdErr($err);
        
        CLI::execute($command);
        
        rewind($out);
        rewind($err);
        $out = stream_get_contents($out);
        $err = stream_get_contents($err);
        $this->assertStringContainsString($command, $out);
        $this->assertStringContainsString(PHP_VERSION, $out);
        $this->assertEmpty($err);
    }
    
    public function testExecuteWithTotalTimeout(): void {
        $command = 'php -r sleep(1);';
        $out = tmpfile();
        $err = tmpfile();
        CLI::setStdOut($out);
        CLI::setStdErr($err);
        
        CLI::setTotalTimeout(0.01);
        CLI::setIdleTimeout(0);
        
        $handler = function (int $errno, string $errstr) {
            $this->assertEquals(E_USER_WARNING, $errno);
            return true;
        };
        set_error_handler($handler, E_USER_WARNING);
        $code = CLI::execute($command);
        restore_error_handler();
        
        $this->assertNotEquals(0, $code);
    }
    
    public function testExecuteWithIdleTimeout(): void {
        $command = 'php -r sleep(1);';
        $out = tmpfile();
        $err = tmpfile();
        CLI::setStdOut($out);
        CLI::setStdErr($err);
        
        CLI::setTotalTimeout(0);
        CLI::setIdleTimeout(0.01);
        
        $handler = function (int $errno, string $errstr) {
            $this->assertEquals(E_USER_WARNING, $errno);
            return true;
        };
        set_error_handler($handler, E_USER_WARNING);
        $code = CLI::execute($command);
        restore_error_handler();
        
        $this->assertNotEquals(0, $code);
    }
}

