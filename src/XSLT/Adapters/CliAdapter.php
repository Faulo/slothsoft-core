<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use RuntimeException;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

/**
 * XSLT adapter that shells out to an external command-line processor.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
final class CliAdapter extends GenericAdapter {
    
    private string $path;
    
    private string $args;
    
    /**
     * @param string $path
     * @param string $args
     * @return void
     */
    public function __construct(string $path, string $args) {
        $this->path = $path;
        $this->args = $args;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @param SplFileInfo|null $outputFile
     * @return SplFileInfo
     * @throws RuntimeException
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()
     *
     */
    public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo {
        if (! $outputFile) {
            $outputFile = FileInfoFactory::createTempFile();
        }
        
        $command = escapeshellarg($this->path) . ' ' . sprintf($this->args, escapeshellarg((string) $this->source->toFile()), escapeshellarg((string) $this->template->toFile()), escapeshellarg((string) $outputFile));
        $output = [];
        $result = 0;
        exec($command, $output, $result);
        if ($result !== 0) {
            throw new RuntimeException($command, $result);
        }
        return $outputFile;
    }
    
}
