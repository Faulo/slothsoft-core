<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

class FileWriterMemoryCache implements FileWriterInterface {
    
    private FileWriterInterface $source;
    
    private ?SplFileInfo $result = null;
    
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toFile(): SplFileInfo {
        if ($this->result === null) {
            $this->result = $this->source->toFile();
        }
        return $this->result;
    }
}

