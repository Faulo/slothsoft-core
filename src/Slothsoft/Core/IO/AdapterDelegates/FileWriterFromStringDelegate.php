<?php
namespace Slothsoft\Core\IO\AdapterDelegates;

use Slothsoft\Core\IO\Writable\FileWriterFileFromStringTrait;
use Slothsoft\Core\IO\Writable\FileWriterInterface;

class FileWriterFromStringDelegate implements FileWriterInterface
{
    use FileWriterFileFromStringTrait;
    
    private $delegate;
    private $fileName;
    private $result;
    
    public function __construct(callable $delegate, ?string $fileName = null) {
        $this->delegate = $delegate;
        $this->fileName = $fileName;
    }
    
    public function toString(): string
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert(is_string($this->result), "FileWriterFromStringDelegate must return string!");
        }
        return $this->result;
    }
    
    public function toFileName() : string {
        return $this->fileName ?? $this->toFile()->getFilename();
    }
}

