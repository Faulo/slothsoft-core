<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

final class FileWriterMemoryCache implements FileWriterInterface {
    
    private FileWriterInterface $source;
    
    private ?SplFileInfo $result = null;
    
    /**
     * @param FileWriterInterface $source
     */
    public function __construct(FileWriterInterface $source) {
        $this->source = $source;
    }
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        if ($this->result === null) {
            $this->result = $this->source->toFile();
        }
        return $this->result;
    }
}
