<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use DOMDocument;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\IO\Writable\Traits\DOMWriterElementFromDocumentTrait;
use SplFileInfo;

final class DOMWriterFileCache implements DOMWriterInterface, FileWriterInterface {
    use FileCacheTrait;
    use DOMWriterElementFromDocumentTrait;
    
    private DOMWriterInterface $sourceWriter;
    
    private ?DOMDocument $document = null;
    
    public function __construct(DOMWriterInterface $sourceWriter, SplFileInfo $cacheFile, ?callable $shouldRefreshCacheDelegate = null) {
        $this->sourceWriter = $sourceWriter;
        $this->initializeFileCache($cacheFile, $shouldRefreshCacheDelegate);
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
}
