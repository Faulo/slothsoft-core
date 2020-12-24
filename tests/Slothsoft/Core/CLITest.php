<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class CLITest extends TestCase {
    
    public function testGetTotalTimeout() {
        $this->assertEquals(-1, CLI::getTotalTimeout());
    }

    public function testSetTotalTimeout() {
        CLI::setTotalTimeout(42);
        $this->assertEquals(42, CLI::getTotalTimeout(42));
    }
    
    public function testGetIdleTimeout() {
        $this->assertEquals(-1, CLI::getIdleTimeout());
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
}

