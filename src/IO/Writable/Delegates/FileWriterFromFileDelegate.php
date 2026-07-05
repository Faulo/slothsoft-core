<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Delegates;

use Closure;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

final class FileWriterFromFileDelegate implements FileWriterInterface {
    
    private Closure $delegate;
    
    private ?SplFileInfo $result = null;
    
    /**
     * @param callable $delegate
     */
    public function __construct(callable $delegate) {
        $this->delegate = Closure::fromCallable($delegate);
    }
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        if ($this->result === null) {
            $this->result = ($this->delegate)();
            assert($this->result instanceof SplFileInfo, "FileWriterFromFileDelegate must return SplFileInfo!");
        }
        return $this->result;
    }
}
