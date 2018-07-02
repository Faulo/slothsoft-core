<?php
namespace Slothsoft\Core\IO\AdapterDelegates;

use Slothsoft\Core\IO\Writable\FileWriterFileFromStringTrait;
use Slothsoft\Core\IO\Writable\FileWriterInterface;

class FileWriterFromStringDelegate implements FileWriterInterface
{
    use FileWriterFileFromStringTrait;
    
    private $delegate;
    private $result;
    
    public function __construct(callable $delegate) {
        $this->delegate = $delegate;
    }
    
    public function toString(): string
    {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert(is_string($this->result), "FileWriterFromStringDelegate must return string!");
        }
        return $this->result;
    }
}

