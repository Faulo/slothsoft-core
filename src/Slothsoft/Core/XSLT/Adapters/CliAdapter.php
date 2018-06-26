<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use SplFileInfo;
use DOMDocument;
use Slothsoft\Core\IO\FileInfoFactory;

/**
 *
 * @author Daniel Schulz
 *        
 */
class CliAdapter extends GenericAdapter
{

    private $path;

    private $args;

    /**
     */
    public function __construct(string $path, string $args)
    {
        $this->path = $path;
        $this->args = $args;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()
     *
     */
    public function writeFile(SplFileInfo $outputFile = null): SplFileInfo
    {
        if (! $outputFile) {
            $outputFile = FileInfoFactory::createTempFile();
        }
        
        $command = escapeshellarg($this->path) . ' ' . sprintf($this->args, escapeshellarg((string) $this->source->toFile()), escapeshellarg((string) $this->template->toFile()), escapeshellarg((string) $outputFile));
        exec($command, $output, $res);
        if ($res !== 0) {
            die($command);
        }
        return $outputFile;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toDocument()
     *
     */
    public function writeDocument(): DOMDocument
    {
        return $this->writeFile()->getDocument();
    }
}

