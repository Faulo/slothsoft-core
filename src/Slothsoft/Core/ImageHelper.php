<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use Slothsoft\Core\IO\FileInfoFactory;
use Imagick;
use ImagickDraw;
use SplFileInfo;

class ImageHelper {

    public static function convertToPng(SplFileInfo $sourceFile, SplFileInfo $targetFile, ?int $alphaColorIndex = - 1): void {
        $targetDirectory = FileInfoFactory::createFromPath($targetFile->getPath());
        if (! $targetDirectory->isDir()) {
            mkdir((string) $targetDirectory, 0777, true);
        }

        $image = new Imagick((string) $sourceFile);
        $image->setImageFormat('png');
        $image->writeImage((string) $targetFile);

        if ($alphaColorIndex !== - 1) {
            $image = imagecreatefrompng((string) $targetFile);
            imagecolortransparent($image, 0);
            imagepng($image, (string) $targetFile);
        }
    }

    public static function createSpriteSheet(SplFileInfo $targetFile, int $spriteWidth, int $spriteHeight, int $columns = 1, int $rows = 1, SplFileInfo ...$sprites): void {
        $targetDirectory = FileInfoFactory::createFromPath($targetFile->getPath());
        if (! $targetDirectory->isDir()) {
            mkdir((string) $targetDirectory, 0777, true);
        }

        $stack = new Imagick();
        foreach ($sprites as $sprite) {
            $image = new Imagick((string) $sprite);
            $image->setImageExtent($spriteWidth, $spriteHeight);
            $stack->addImage($image);
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
}

