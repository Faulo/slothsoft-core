<?php
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use DOMDocument;
use SplFileInfo;

class DOMWriterFileCache implements DOMWriterInterface, FileWriterInterface
{
    use DOMWriterElementFromDocumentTrait;
    
    private $sourceWriter;
    private $cacheFile;
    private $shouldRefreshCacheDelegate;
    
    private $document;
    
    public function __construct(DOMWriterInterface $sourceWriter, SplFileInfo $cacheFile, callable $shouldRefreshCacheDelegate) {
        $this->sourceWriter = $sourceWriter;
        $this->cacheFile = $cacheFile;
        $this->shouldRefreshCacheDelegate = $shouldRefreshCacheDelegate;
    }

    public function toFile(): SplFileInfo
    {
        $this->refreshCacheFile();
        return $this->cacheFile;
    }
    
    public function toDocument(): DOMDocument
    {
        $this->refreshCacheFile();
        if ($this->document === null) {
            $this->document->load((string) $this->cacheFile);
        }
    }
    
    private function refreshCacheFile() : void {
        $shouldRefreshCache = true;
        if (is_dir($this->cacheFile->getPath())) {
            if ($this->cacheFile->isFile()) {
                $shouldRefreshCache = ($this->shouldRefreshCacheDelegate)($this->cacheFile);
            }
        } else {
            mkdir($this->cacheFile->getPath(), 0777, true);
        }
        
        if ($shouldRefreshCache) {
            $this->document = $this->sourceWriter->toDocument();
            $this->document->save((string) $this->cacheFile);
        }
    }

}

