<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Writable\Decorators;

use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Generator;
use SplFileInfo;

class ChunkWriterFileCache implements ChunkWriterInterface, FileWriterInterface
{    
    private $sourceWriter;
    private $cacheFile;
    private $shouldRefreshCacheDelegate;
    public function __construct(ChunkWriterInterface $sourceWriter, SplFileInfo $cacheFile, callable $shouldRefreshCacheDelegate) {
        $this->sourceWriter = $sourceWriter;
        $this->cacheFile = $cacheFile;
        $this->shouldRefreshCacheDelegate = $shouldRefreshCacheDelegate;
    }
    
    public function toChunks(): Generator
    {
        if ($this->shouldRefreshCache()) {
            $handle = $this->cacheFile->openFile(StreamWrapperInterface::MODE_CREATE_WRITEONLY);
            foreach ($this->sourceWriter->toChunks() as $chunk) {
                $handle->fwrite($chunk);
                yield $chunk;
            }
            unset($handle);
        } else {
            $handle = $this->cacheFile->openFile(StreamWrapperInterface::MODE_OPEN_READONLY);
            while (!$handle->eof()) {
                yield $handle->fread(Memory::ONE_KILOBYTE);
            }
            unset($handle);
        }
    }
    
    public function toFile(): SplFileInfo
    {
        if ($this->shouldRefreshCache()) {
            $handle = $this->cacheFile->openFile(StreamWrapperInterface::MODE_CREATE_WRITEONLY);
            foreach ($this->sourceWriter->toChunks() as $chunk) {
                $handle->fwrite($chunk);
            }
            unset($handle);
        }
        return $this->cacheFile;
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

