<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use Closure;
use SplFileInfo;

trait FileCacheTrait {
    
    private SplFileInfo $cacheFile;
    
    private ?Closure $shouldRefreshCacheDelegate;
    
    private function initializeFileCache(SplFileInfo $cacheFile, ?callable $shouldRefreshCacheDelegate): void {
        $this->cacheFile = $cacheFile;
        $this->shouldRefreshCacheDelegate = $shouldRefreshCacheDelegate === null ? null : Closure::fromCallable($shouldRefreshCacheDelegate);
    }
    
    private function shouldRefreshCache(): bool {
        $shouldRefreshCache = true;
        if (is_dir($this->cacheFile->getPath())) {
            if ($this->cacheFile->isFile() and $this->cacheFile->getSize() > 0) {
                $shouldRefreshCache = $this->shouldRefreshCacheDelegate === null ? false : ($this->shouldRefreshCacheDelegate)($this->cacheFile);
            }
        } else {
            mkdir($this->cacheFile->getPath(), 0777, true);
        }
        return $shouldRefreshCache;
    }
}
