<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use DomainException;

/**
 * Legacy image conversion, resizing, thumbnail, and optimization facade.
 *
 * @author Daniel Schulz
 * @since 2017-12-28
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
final class Image {
    
    public static bool $IRFANVIEW_ACTIVE = true;
    
    public static string $IRFANVIEW_PATH = 'i_view32';
    
    const IRFANVIEW_QUERY_SIZE = '%1$s /resize=(%3$d,%4$d) /aspectratio /silent /convert=%2$s';
    
    const IRFANVIEW_QUERY_CONVERT = '%1$s /silent /convert=%2$s';
    
    const IRFANVIEW_QUERY_SCALE = '%1$s /silent /resize=(%3$d,%4$d) /convert=%2$s';
    
    public static bool $FFMPEG_ACTIVE = true;
    
    // DEAKTIVIERT: temp.tmp kann nicht gelesen werden!!!!!
    public static string $FFMPEG_PATH = 'ffmpeg';
    
    const FFMPEG_QUERY_CONVERT = '-i %1$s %2$s -y';
    
    // /encode
    public static bool $OPTIPNG_ACTIVE = true;
    
    public static string $OPTIPNG_PATH = 'optipng';
    
    const OPTIPNG_QUERY_MINIFY = '-silent %1$s';
    
    public static int $THUMBNAIL_WIDTH = 320;
    
    public static int $THUMBNAIL_HEIGHT = 240;
    
    /**
     * @return string
     */
    protected static function getTempFile(): string {
        // $ret = tempnam(sys_get_temp_dir(), __CLASS__);
        $ret = temp_file(__CLASS__);
        // my_dump($ret);
        return $ret;
    }
    
    /**
     * @param mixed $destFile
     * @param mixed $spriteWidth
     * @param mixed $spriteHeight
     * @param mixed $cols
     * @param mixed $rows
     * @param array $imageList
     * @return bool
     */
    public static function createSprite($destFile, $spriteWidth, $spriteHeight, $cols = 1, $rows = 1, array $imageList = []): bool {
        $width = $spriteWidth * $cols;
        $height = $spriteHeight * $rows;
        $ret = imagepng(imagecreatetruecolor($width, $height), $destFile, 9);
        if ($ret and $imageList) {
            self::addSprite($destFile, $imageList, $spriteWidth, $spriteHeight);
        }
        return $ret;
    }
    
    /**
     * @param mixed $destFile
     * @param array $sourceFileList
     * @param mixed $spriteWidth
     * @param mixed $spriteHeigh
     * @return bool
     */
    public static function addSprite($destFile, array $sourceFileList, $spriteWidth = null, $spriteHeigh = null): bool {
        $destInfo = self::imageInfo($destFile);
        $destImage = self::createFromFile($destFile);
        imagealphablending($destImage, true);
        foreach ($sourceFileList as $index => $sourceFile) {
            $sourceFile = (string) $sourceFile;
            if ($sourceFile) {
                $sourceInfo = self::imageInfo($sourceFile);
                
                $width = $spriteWidth === null ? $sourceInfo['width'] : $spriteWidth;
                $height = $spriteHeigh === null ? $sourceInfo['height'] : $spriteHeigh;
                
                $x = $width * $index;
                $y = 0;
                while ($x >= $destInfo['width']) {
                    $x -= $destInfo['width'];
                    $y += $height;
                }
                $sourceImage = self::createFromFile($sourceFile);
                imagecopy($destImage, $sourceImage, $x, $y, 0, 0, $sourceInfo['width'], $sourceInfo['height']);
                imagedestroy($sourceImage);
            }
        }
        return imagepng($destImage, $destFile, 9);
    }
    
    /**
     * @param mixed $file
     * @return array
     */
    public static function imageInfo($file): array {
        $arr = getimagesize($file);
        return [
            'width' => $arr[0],
            'height' => $arr[1],
            'mime' => $arr['mime']
        ];
    }
    
    /**
     * @param mixed $file
     * @return mixed
     * @throws DomainException
     */
    public static function createFromFile($file) {
        $size = getimagesize($file);
        switch ($size['mime']) {
            case 'image/jpeg':
                return imagecreatefromjpeg($file); // jpeg file
            case 'image/gif':
                return imagecreatefromgif($file); // gif file
            case 'image/png':
                return imagecreatefrompng($file); // png file
            default:
                throw new DomainException(sprintf('MIME TYPE NOT SUPPORTED: "%s"', basename($file)));
        }
    }
    
    /**
     * @param mixed $image_source
     * @param mixed $new_image
     * @return void
     */
    public static function setTransparency($image_source, $new_image) {
        $transparencyIndex = imagecolortransparent($image_source);
        $transparencyColor = array(
            'red' => 255,
            'green' => 255,
            'blue' => 255
        );
        
        if ($transparencyIndex > -1) {
            $transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);
        }
        
        $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($new_image, 0, 0, $transparencyIndex);
        imagecolortransparent($new_image, $transparencyIndex);
    }
    
    /**
     * @param mixed $file
     * @param mixed $x
     * @param mixed $y
     * @param mixed $new_width
     * @param mixed $new_height
     * @return mixed
     */
    public static function splitImage($file, $x, $y, $new_width, $new_height) {
        $pic = self::createFromFile($file);
        $new_image = imagecreatetruecolor($new_width, $new_height);
        self::setTransparency($pic, $new_image);
        $originaltransparentcolor = imagecolortransparent($pic);
        if ($originaltransparentcolor > -1) {
            $transparentcolor = imagecolorsforindex($pic, $originaltransparentcolor);
        }
        imagecopyresized($new_image, $pic, 0, 0, $x, $y, $new_width, $new_height, $new_width, $new_height);
        if ($originaltransparentcolor > -1) {
            $newtransparentcolor = imagecolorallocate($new_image, $transparentcolor['red'], $transparentcolor['green'], $transparentcolor['blue']);
            imagecolortransparent($new_image, $newtransparentcolor);
        }
        imagedestroy($pic);
        return $new_image;
    }
    
    /**
     * @param mixed $sourceFile
     * @param mixed $destFile
     * @return bool
     */
    public static function convertFile($sourceFile, $destFile): bool {
        $sourceFile = str_replace('/', '\\', $sourceFile);
        $destFile = str_replace('/', '\\', $destFile);
        if (self::$IRFANVIEW_ACTIVE) {
            $command = '"' . self::$IRFANVIEW_PATH . '" ';
            $command .= sprintf(self::IRFANVIEW_QUERY_CONVERT, escapeshellarg($sourceFile), escapeshellarg($destFile));
            exec($command);
        } elseif (self::$FFMPEG_ACTIVE) {
            $command = '"' . self::$FFMPEG_PATH . '" ';
            $command .= sprintf(self::FFMPEG_QUERY_CONVERT, escapeshellarg($sourceFile), escapeshellarg($destFile));
            exec($command);
        }
        $ret = file_exists($destFile);
        
        if (self::$OPTIPNG_ACTIVE) {
            if ($ret and strtolower(pathinfo($destFile, PATHINFO_EXTENSION)) === 'png') {
                $command = '"' . self::$OPTIPNG_PATH . '" ';
                $command .= sprintf(self::OPTIPNG_QUERY_MINIFY, escapeshellarg($destFile));
                exec($command);
            }
        }
        
        return $ret;
    }
    
    /**
     * @param mixed $sourceFile
     * @param mixed $destFile
     * @param mixed $width
     * @param mixed $height
     * @return bool
     */
    public static function scaleFile($sourceFile, $destFile, $width, $height): bool {
        $sourceFile = str_replace('/', '\\', $sourceFile);
        $destFile = str_replace('/', '\\', $destFile);
        if (self::$IRFANVIEW_ACTIVE) {
            $command = '"' . self::$IRFANVIEW_PATH . '" ';
            $command .= sprintf(self::IRFANVIEW_QUERY_SCALE, escapeshellarg($sourceFile), escapeshellarg($destFile), $width, $height);
            exec($command);
        }
        
        $ret = file_exists($destFile);
        
        if (self::$OPTIPNG_ACTIVE) {
            if ($ret and strtolower(pathinfo($destFile, PATHINFO_EXTENSION)) === 'png') {
                $command = '"' . self::$OPTIPNG_PATH . '" ';
                $command .= sprintf(self::OPTIPNG_QUERY_MINIFY, escapeshellarg($destFile));
                exec($command);
            }
        }
        
        return $ret;
    }
    
    /**
     * @param mixed $sourceFile
     * @param mixed $destFile
     * @param mixed $width
     * @param mixed $height
     * @return void
     */
    public static function cropFile($sourceFile, $destFile, $width, $height) {
    }
    
    /**
     * @param mixed $sourceFile
     * @param mixed $appendFile
     * @param mixed $targetFile
     * @return bool
     */
    public static function mergeFile($sourceFile, $appendFile, $targetFile = null): bool {
        if ($targetFile === null) {
            $targetFile = $sourceFile;
        }
        $ret = false;
        
        $sourceImage = self::createFromFile($sourceFile);
        $appendImage = self::createFromFile($appendFile);
        
        if ($sourceImage and $appendImage) {
            imagealphablending($sourceImage, true);
            imagesavealpha($sourceImage, true);
            
            $appendInfo = self::imageInfo($appendFile);
            imagecopy($sourceImage, $appendImage, 0, 0, 0, 0, $appendInfo['width'], $appendInfo['height']);
            
            $tempFile = self::getTempFile();
            imagepng($sourceImage, $tempFile);
            $ret = self::convertFile($tempFile, $targetFile);
        }
        return $ret;
    }
}
