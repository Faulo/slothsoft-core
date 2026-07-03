<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Psr7;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;

/**
 * ProcessStreamTest
 *
 * @see ProcessStream
 */
final class ProcessStreamTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(ProcessStream::class), "Failed to load class 'Slothsoft\Core\IO\Psr7\ProcessStream'!");
    }
    
    /**
     *
     * @test
     */
    public function readInitializesProcess(): void {
        $sut = new ProcessStream($this->createPhpCommand('abc'));
        
        $this->assertSame('a', $sut->read(1));
    }
    
    /**
     *
     * @test
     */
    public function getContentsInitializesProcess(): void {
        $sut = new ProcessStream($this->createPhpCommand('abc'));
        
        $this->assertSame('abc', $sut->getContents());
    }
    
    /**
     *
     * @test
     */
    public function getContentsReadsOutputLargerThanOneChunk(): void {
        $output = str_repeat('a', 9000);
        $sut = new ProcessStream($this->createPhpCommand($output));
        
        $this->assertSame($output, $sut->getContents());
    }
    
    private function createPhpCommand(string $output): string {
        $script = FileInfoFactory::createFromString('<?php echo ' . var_export($output, true) . ';');
        
        return escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg((string) $script);
    }
}
