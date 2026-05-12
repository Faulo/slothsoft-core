<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use Closure;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

class FileWriterFromFileDelegate implements FileWriterInterface {
    
    private Closure $delegate;
    
    private ?SplFileInfo $result = null;
    
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    public function toFile(): SplFileInfo {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof SplFileInfo, "FileWriterFromFileDelegate must return SplFileInfo!");
        }
        return $this->result;
    }
}

