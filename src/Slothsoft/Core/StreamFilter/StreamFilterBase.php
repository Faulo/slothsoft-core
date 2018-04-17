<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

abstract class StreamFilterBase extends \php_user_filter implements StreamFilterInterface
{
    private $opening;
    public function onCreate() {
        $this->opening = true;
    }
    public final function filter($in, $out, &$consumed, $closing) {
        if ($this->opening) {
            $this->opening = false;
            $data = $this->processHeader();
            if ($data !== '') {
                stream_bucket_append($out, $this->createBucket($data));
            }
        }
        while ($inBucket = stream_bucket_make_writeable($in)) {
            $consumed += $inBucket->datalen;
            $data = $this->processPayload($inBucket->data);
            if ($data !== '') {
                stream_bucket_append($out, $this->createBucket($data));
            }
        }
        if ($closing) {
            $data = $this->processFooter();
            if ($data !== '') {
                stream_bucket_append($out, $this->createBucket($data));
            }
        }
        return PSFS_PASS_ON;
    }
    private function createBucket(string $data) {
        return stream_bucket_new($this->stream, $data);
    }
    
    abstract protected function processHeader() : string;
    abstract protected function processPayload(string $input) : string;
    abstract protected function processFooter() : string;
}

