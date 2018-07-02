<?php
namespace Slothsoft\Core\IO\AdapterDelegates;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterStringFromFileTrait;
use SplFileInfo;

class FileWriterFromFileDelegate implements FileWriterInterface
{
    use FileWriterStringFromFileTrait;
    
    private $delegate;
    private $fileName;
    private $result;
    
    public function __construct(callable $delegate, ?string $fileName = null) {
        $this->delegate = $delegate;
        $this->fileName = $fileName;
    }
    
    public function toFile(): SplFileInfo
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof SplFileInfo, "FileWriterFromFileDelegate must return SplFileInfo!");
        }
        return $this->result;
    }
    
    public function toFileName() : string {
        return $this->fileName ?? $this->toFile()->getFilename();
    }
}

