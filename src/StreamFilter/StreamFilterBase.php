<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

abstract class StreamFilterBase extends \php_user_filter implements StreamFilterInterface {

    const STATE_OPENING = 1;

    const STATE_PROCESSING = 2;

    const STATE_CLOSING = 3;

    const STATE_CLOSED = 4;

    private $state;

    public function onCreate(): bool {
        $this->state = self::STATE_OPENING;
        return true;
    }

    public final function filter($in, $out, &$consumed, $closing): int {
        if ($this->state === self::STATE_OPENING) {
            $data = $this->processHeader();
            if ($data !== '') {
                stream_bucket_append($out, $this->createBucket($data));
            }
            $this->state = self::STATE_PROCESSING;
        }
        if ($this->state === self::STATE_PROCESSING) {
            while ($inBucket = stream_bucket_make_writeable($in)) {
                $consumed += $inBucket->datalen;
                $data = $this->processPayload($inBucket->data);
                if ($data !== '') {
                    stream_bucket_append($out, $this->createBucket($data));
                }
            }
            if ($closing or feof($this->stream)) {
                $this->state = self::STATE_CLOSING;
            }
        }
        if ($this->state === self::STATE_CLOSING) {
            $data = $this->processFooter();
            if ($data !== '') {
                stream_bucket_append($out, $this->createBucket($data));
            }
            $this->state = self::STATE_CLOSED;
        }
        return PSFS_PASS_ON;
    }

    private function createBucket(string $data) {
        return stream_bucket_new($this->stream, $data);
    }

    abstract protected function processHeader(): string;

    abstract protected function processPayload(string $input): string;

    abstract protected function processFooter(): string;
}

