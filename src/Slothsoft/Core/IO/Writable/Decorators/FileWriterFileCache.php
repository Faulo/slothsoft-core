<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

class FileWriterFileCache implements FileWriterInterface
{
    private $sourceWriter;
    private $cacheFile;
    private $shouldRefreshCacheDelegate;
    public function __construct(FileWriterInterface $sourceWriter, SplFileInfo $cacheFile, callable $shouldRefreshCacheDelegate) {
        $this->sourceWriter = $sourceWriter;
        $this->cacheFile = $cacheFile;
        $this->shouldRefreshCacheDelegate = $shouldRefreshCacheDelegate;
    }

    public function toFile(): SplFileInfo
    {
        $this->refreshCacheFile();
        return $this->cacheFile;
    }
    
    private function refreshCacheFile() : void {
        if ($this->shouldRefreshCache()) {
            copy((string) $this->sourceWriter->toFile(), (string) $this->cacheFile);
        }
    }
    
    private function shouldRefreshCache() : bool {
        $shouldRefreshCache = true;
        if (is_dir($this->cacheFile->getPath())) {
            if ($this->cacheFile->isFile() and $this->cacheFile->getSize() > 0) {
                $shouldRefreshCache = ($this->shouldRefreshCacheDelegate)($this->cacheFile);
            }
        } else {
            mkdir($this->cacheFile->getPath(), 0777, true);
        }
        return $shouldRefreshCache;
    }
}

