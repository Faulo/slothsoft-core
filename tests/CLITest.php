<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class CLITest extends TestCase {

    public function testDefaultTotalTimeout() {
        $this->assertEquals(0, CLI::getTotalTimeout());
    }

    public function testSetTotalTimeout() {
        CLI::setTotalTimeout(42);
        $this->assertEquals(42, CLI::getTotalTimeout(42));
    }

    public function testDefaultIdleTimeout() {
        $this->assertEquals(0, CLI::getIdleTimeout());
    }

    public function testSetIdleTimeout() {
        CLI::setIdleTimeout(42);
        $this->assertEquals(42, CLI::getIdleTimeout(42));
    }

    public function testDefaultStdOut() {
        $this->assertEquals(STDOUT, CLI::getStdOut());
    }

    public function testDefaultStdErr() {
        $this->assertEquals(STDERR, CLI::getStdErr());
    }

    public function testSetStdOut() {
        $handle = tmpfile();
        CLI::setStdOut($handle);
        $this->assertEquals($handle, CLI::getStdOut());
    }

    public function testSetStdErr() {
        $handle = tmpfile();
        CLI::setStdErr($handle);
        $this->assertEquals($handle, CLI::getStdErr());
    }

    public function testBasicExecute() {
        $command = 'php --version';
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

    public function testExecuteWithTotalTimeout() {
        $command = 'php -r sleep(1);';
        $out = tmpfile();
        $err = tmpfile();
        CLI::setStdOut($out);
        CLI::setStdErr($err);

        CLI::setTotalTimeout(0.01);
        CLI::setIdleTimeout(0);
        
        $handler = function(int $errno, string $errstr) {
            $this->assertEquals(E_USER_WARNING, $errno);
        };
        set_error_handler($handler, E_USER_WARNING);
        $code = CLI::execute($command);
        restore_error_handler();
        
        $this->assertEquals(1, $code);
    }

    public function testExecuteWithIdleTimeout() {
        $command = 'php -r sleep(1);';
        $out = tmpfile();
        $err = tmpfile();
        CLI::setStdOut($out);
        CLI::setStdErr($err);

        CLI::setTotalTimeout(0);
        CLI::setIdleTimeout(0.01);
        
        $handler = function(int $errno, string $errstr) {
            $this->assertEquals(E_USER_WARNING, $errno);
        };
        set_error_handler($handler, E_USER_WARNING);
        $code = CLI::execute($command);
        restore_error_handler();
        
        $this->assertEquals(1, $code);
    }
}

