<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO\Writable\Decorators;

use Generator;
use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\IO\Writable\ChunkWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use SplFileInfo;

final class ChunkWriterFileCache implements ChunkWriterInterface, FileWriterInterface {
    use FileCacheTrait;
    
    private ChunkWriterInterface $sourceWriter;
    
    /**
     * @param ChunkWriterInterface $sourceWriter
     * @param SplFileInfo $cacheFile
     * @param callable|null $shouldRefreshCacheDelegate
     */
    public function __construct(ChunkWriterInterface $sourceWriter, SplFileInfo $cacheFile, ?callable $shouldRefreshCacheDelegate = null) {
        $this->sourceWriter = $sourceWriter;
        $this->initializeFileCache($cacheFile, $shouldRefreshCacheDelegate);
    }
    
    /**
     * @return Generator
     */
    public function toChunks(): Generator {
        if ($this->shouldRefreshCache()) {
            $handle = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
            foreach ($this->sourceWriter->toChunks() as $chunk) {
                fwrite($handle, $chunk);
                yield $chunk;
            }
            rewind($handle);
            file_put_contents((string) $this->cacheFile, $handle);
            fclose($handle);
        } else {
            $handle = $this->cacheFile->openFile(StreamWrapperInterface::MODE_OPEN_READONLY);
            while (! $handle->eof()) {
                yield $handle->fread(Memory::ONE_KILOBYTE);
            }
            unset($handle);
        }
    }
    
    /**
     * @return SplFileInfo
     */
    public function toFile(): SplFileInfo {
        if ($this->shouldRefreshCache()) {
            $handle = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
            foreach ($this->sourceWriter->toChunks() as $chunk) {
                fwrite($handle, $chunk);
            }
            rewind($handle);
            file_put_contents((string) $this->cacheFile, $handle);
            fclose($handle);
        }
        return $this->cacheFile;
    }
}
