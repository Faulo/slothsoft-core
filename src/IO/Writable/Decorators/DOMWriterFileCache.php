<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use Closure;
use DOMDocument;
use SplFileInfo;

class DOMWriterFileCache implements DOMWriterInterface, FileWriterInterface {
    use DOMWriterElementFromDocumentTrait;
    
    private DOMWriterInterface $sourceWriter;
    
    private SplFileInfo $cacheFile;
    
    private ?Closure $shouldRefreshCacheDelegate;
    
    private ?DOMDocument $document = null;
    
    public function __construct(DOMWriterInterface $sourceWriter, SplFileInfo $cacheFile, ?callable $shouldRefreshCacheDelegate = null) {
        $this->sourceWriter = $sourceWriter;
        $this->cacheFile = $cacheFile;
        $this->shouldRefreshCacheDelegate = $shouldRefreshCacheDelegate === null ? null : Closure::fromCallable($shouldRefreshCacheDelegate);
    }
    
    public function toFile(): SplFileInfo {
        $this->refreshCacheFile();
        return $this->cacheFile;
    }
    
    public function toDocument(): DOMDocument {
        $this->refreshCacheFile();
        if ($this->document === null) {
            $this->document = DOMHelper::loadDocument((string) $this->cacheFile);
        }
        return $this->document;
    }
    
    private function refreshCacheFile(): void {
        if ($this->shouldRefreshCache()) {
            $this->document = $this->sourceWriter->toDocument();
            $this->document->save((string) $this->cacheFile);
        }
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

