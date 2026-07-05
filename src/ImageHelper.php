<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use Imagick;
use ImagickDraw;
use SplFileInfo;

/**
 * Legacy Imagick helper for PNG conversion and sprite sheet generation.
 *
 * @author Daniel Schulz
 * @since 2018-07-02
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
final class ImageHelper {
    
    /**
     * @param SplFileInfo $sourceFile
     * @param SplFileInfo $targetFile
     * @param int|null $alphaColorIndex
     * @return void
     */
    public static function convertToPng(SplFileInfo $sourceFile, SplFileInfo $targetFile, ?int $alphaColorIndex = -1): void {
        FileSystem::ensureDirectory($targetFile->getPath());
        
        $image = new Imagick((string) $sourceFile);
        $image->setImageFormat('png');
        $image->writeImage((string) $targetFile);
        
        if ($alphaColorIndex !== -1) {
            $image = imagecreatefrompng((string) $targetFile);
            imagecolortransparent($image, 0);
            imagepng($image, (string) $targetFile);
        }
    }
    
    /**
     * @param SplFileInfo $targetFile
     * @param int $spriteWidth
     * @param int $spriteHeight
     * @param int $columns
     * @param int $rows
     * @param Imagick ...$sprites
     * @return void
     */
    public static function createSpriteSheetFromImages(SplFileInfo $targetFile, int $spriteWidth, int $spriteHeight, int $columns = 1, int $rows = 1, Imagick ...$sprites): void {
        FileSystem::ensureDirectory($targetFile->getPath());
        
        $stack = new Imagick();
        foreach ($sprites as $sprite) {
            $sprite->setImageExtent($spriteWidth, $spriteHeight);
            $stack->addImage($sprite);
        }
        $stack->resetIterator();
        
        if ($columns === 1) {
            $sheet = $stack->appendimages(true);
        } elseif ($rows === 1) {
            $sheet = $stack->appendimages(false);
        } else {
            $sheet = $stack->montageImage(new ImagickDraw(), "{$columns}x$rows", "{$spriteWidth}x$spriteHeight", Imagick::MONTAGEMODE_CONCATENATE, '0');
        }
        $sheet->setImageFormat('png');
        $sheet->writeImage((string) $targetFile);
    }
    
    /**
     * @param SplFileInfo $targetFile
     * @param int $spriteWidth
     * @param int $spriteHeight
     * @param int $columns
     * @param int $rows
     * @param SplFileInfo ...$spriteFiles
     * @return void
     */
    public static function createSpriteSheet(SplFileInfo $targetFile, int $spriteWidth, int $spriteHeight, int $columns = 1, int $rows = 1, SplFileInfo ...$spriteFiles): void {
        $sprites = [];
        foreach ($spriteFiles as $spriteFile) {
            $sprites[] = new Imagick((string) $spriteFile);
        }
        
        self::createSpriteSheetFromImages($targetFile, $spriteWidth, $spriteHeight, $columns, $rows, ...$sprites);
    }
}
