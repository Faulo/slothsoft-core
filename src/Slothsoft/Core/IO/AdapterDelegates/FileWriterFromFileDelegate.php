<?php
namespace Slothsoft\Core\IO\AdapterDelegates;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterStringFromFileTrait;
use SplFileInfo;

class FileWriterFromFileDelegate implements FileWriterInterface
{
    use FileWriterStringFromFileTrait;
    
    private $delegate;
    private $result;
    
    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }
    
    public function toFile(): SplFileInfo
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof SplFileInfo, "FileWriterFromFileDelegate must return SplFileInfo!");
        }
        return $this->result;
    }
}

