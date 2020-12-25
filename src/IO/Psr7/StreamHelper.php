<?php
declare(strict_types = 1);
namespace Slothsoft\Core\IO\Psr7;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

class StreamHelper {

    public static function cacheStream(StreamInterface $input, $chunkSize = Memory::ONE_KILOBYTE): StreamInterface {
        $cache = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        while (! $input->eof()) {
            fwrite($cache, $input->read($chunkSize));
        }
        rewind($cache);
        return new Stream($cache);
    }

    public static function sliceStream(StreamInterface $input, int $offset, int $length): StreamInterface {
        $input = self::cacheStream($input);
        $cache = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        $input->seek($offset);
        $totalLength = 0;
        while (! $input->eof() and $totalLength < $length) {
            $dataLength = $length - $totalLength;
            $data = $input->read($dataLength);
            $readLength = strlen($data);
            if ($readLength > $dataLength) {
                fwrite($cache, substr($data, 0, $dataLength));
                $totalLength += $dataLength;
            } else {
                fwrite($cache, $data);
                $totalLength += $readLength;
            }
        }
        rewind($cache);
        return new Stream($cache);
    }
}

