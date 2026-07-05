<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\FileWriterInterface;
use SplFileInfo;

final class FileWriterFileCache implements FileWriterInterface {
    use FileCacheTrait;
    
    private FileWriterInterface $sourceWriter;
    
    /**
     * @param FileWriterInterface $sourceWriter
     * @param SplFileInfo $cacheFile
     * @param callable|null $shouldRefreshCacheDelegate
     * @return void
     */
    public function __construct(FileWriterInterface $sourceWriter, SplFileInfo $cacheFile, ?callable $shouldRefreshCacheDelegate = null) {
        $this->sourceWriter = $sourceWriter;
        $this->initializeFileCache($cacheFile, $shouldRefreshCacheDelegate);
    }
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        $this->refreshCacheFile();
        return $this->cacheFile;
    }
    
    private function refreshCacheFile(): void {
        if ($this->shouldRefreshCache()) {
            copy((string) $this->sourceWriter->toFile(), (string) $this->cacheFile);
        }
    }
}
