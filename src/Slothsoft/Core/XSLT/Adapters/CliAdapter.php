<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT\Adapters;

use Slothsoft\Core\IO\HTTPFile;
use DOMDocument;

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
    public function writeFile(HTTPFile $outputFile = null): HTTPFile
    {
        if (! $outputFile) {
            $outputFile = HTTPFile::createFromTemp();
        }
        
        $command = escapeshellarg($this->path) . ' ' . sprintf($this->args, escapeshellarg($this->source->toFile()->getPath()), escapeshellarg($this->template->toFile()->getPath()), escapeshellarg($outputFile->getPath()));
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

