<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase {
    
    public function testNormalizeSlashes() {
        $input = '/A\\B';
        
        $this->assertEquals(DIRECTORY_SEPARATOR . 'A' . DIRECTORY_SEPARATOR . 'B', normalize_slashes($input));
    }
    
    public function testNormalizeSlashesCollapsesRepeatedSeparators(): void {
        $input = '//A\\\\//B///';
        
        $this->assertSame(DIRECTORY_SEPARATOR . 'A' . DIRECTORY_SEPARATOR . 'B' . DIRECTORY_SEPARATOR, normalize_slashes($input));
        $this->assertSame('', normalize_slashes(''));
        $this->assertSame('A' . DIRECTORY_SEPARATOR . 'B', normalize_slashes('A//B'));
    }
    
    public function testTempDirectoryAcceptsMethodName(): void {
        $directory = temp_dir(__METHOD__);
        $expectedParent = sys_get_temp_dir() . DIRECTORY_SEPARATOR . normalize_slashes(__CLASS__) . DIRECTORY_SEPARATOR . __FUNCTION__;
        
        $this->assertDirectoryExists($directory);
        $this->assertSame($expectedParent, dirname($directory));
        $file = $directory . DIRECTORY_SEPARATOR . 'test.txt';
        $this->assertSame(4, file_put_contents($file, 'test'));
        $this->assertSame('test', file_get_contents($file));
    }
    
    public function testTempFileAcceptsMethodName(): void {
        $file = temp_file(__METHOD__);
        $expectedParent = sys_get_temp_dir() . DIRECTORY_SEPARATOR . normalize_slashes(__CLASS__) . DIRECTORY_SEPARATOR . __FUNCTION__;
        
        $this->assertDirectoryExists(dirname($file));
        $this->assertSame($expectedParent, dirname($file));
        $this->assertFileDoesNotExist($file);
        $this->assertSame(4, file_put_contents($file, 'test'));
        $this->assertSame('test', file_get_contents($file));
    }
}
