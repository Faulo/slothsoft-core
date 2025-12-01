<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use Imagick;
use ImagickDraw;
use SplFileInfo;

class ImageHelper {
    
    public static function convertToPng(SplFileInfo $sourceFile, SplFileInfo $targetFile, ?int $alphaColorIndex = - 1): void {
        FileSystem::ensureDirectory($targetFile->getPath());
        
        $image = new Imagick((string) $sourceFile);
        $image->setImageFormat('png');
        $image->writeImage((string) $targetFile);
        
        if ($alphaColorIndex !== - 1) {
            $image = imagecreatefrompng((string) $targetFile);
            imagecolortransparent($image, 0);
            imagepng($image, (string) $targetFile);
        }
    }
    
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
            $sheet = $stack->montageImage(new ImagickDraw(), "{$columns}x{$rows}", "{$spriteWidth}x{$spriteHeight}", Imagick::MONTAGEMODE_CONCATENATE, '0');
        }
        $sheet->setImageFormat('png');
        $sheet->writeImage((string) $targetFile);
    }
    
    public static function createSpriteSheet(SplFileInfo $targetFile, int $spriteWidth, int $spriteHeight, int $columns = 1, int $rows = 1, SplFileInfo ...$spriteFiles): void {
        $sprites = [];
        foreach ($spriteFiles as $spriteFile) {
            $sprites[] = new Imagick((string) $spriteFile);
        }
        
        self::createSpriteSheetFromImages($targetFile, $spriteWidth, $spriteHeight, $columns, $rows, ...$sprites);
    }
}

