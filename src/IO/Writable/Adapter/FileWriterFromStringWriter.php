<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Adapter;

use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\StringWriterInterface;
use SplFileInfo;

final class FileWriterFromStringWriter implements FileWriterInterface {
    
    private StringWriterInterface $source;
    
    public function __construct(StringWriterInterface $source) {
        $this->source = $source;
    }
    
    public function toFile(): SplFileInfo {
        return FileInfoFactory::createFromString($this->source->toString());
    }
}

