<?php
declare(strict_types = 1);

namespace Slothsoft\Core\IO;

use Slothsoft\Core\Calendar\Seconds;

/**
 * Legacy HTTP-backed stream helper.
 *
 * @author Daniel Schulz
 * @since 2014-05-28
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
abstract class HTTPStream {
    
    const STATUS_ERROR = 0;
    
    const STATUS_DONE = 1;
    
    const STATUS_RETRY = 2;
    
    const STATUS_CONTENT = 3;
    
    const STATUS_CONTENTDONE = 4;
    
    protected $headerList = [];
    
    protected $status = self::STATUS_DONE;
    
    protected $content = '';
    
    protected $mime = null;
    
    protected $encoding = null;
    
    protected $sleepDuration = Seconds::MILLISECOND;
    
    protected $heartbeatContent = null;
    
    // character to send after each heartbeatInterval is reached
    protected $heartbeatEOL = null;
    
    // character to send before new content, when heartbeatContent was sent
    protected $heartbeatInterval = Seconds::SECOND;
    
    // time without actual content before heartbeatContent is sent
    protected $heartbeatTimeout = Seconds::HOUR;
    
    // time without actual content before child is terminated
    
    /**
     * @return mixed
     */
    public function getMime() {
        return $this->mime;
    }
    
    /**
     * @return mixed
     */
    public function getEncoding() {
        return $this->encoding;
    }
    
    /**
     * @return mixed
     */
    public function getHeaderList() {
        return $this->headerList;
    }
    
    /**
     * @return void
     */
    public function setStatusError() {
        $this->status = self::STATUS_ERROR;
    }
    
    /**
     * @return void
     */
    public function setStatusDone() {
        $this->status = self::STATUS_DONE;
    }
    
    /**
     * @return void
     */
    public function setStatusRetry() {
        $this->status = self::STATUS_RETRY;
    }
    
    /**
     * @return void
     */
    public function setStatusContent() {
        $this->status = self::STATUS_CONTENT;
    }
    
    /**
     * @return void
     */
    public function setStatusContentDone() {
        $this->status = self::STATUS_CONTENTDONE;
    }
    
    /**
     * @return mixed
     */
    public function getStatus() {
        $this->parseStatus();
        return $this->status;
    }
    
    /**
     * @return void
     */
    abstract protected function parseStatus();
    
    /**
     * @return mixed
     */
    public function getContent() {
        $this->parseContent();
        return $this->content;
    }
    
    /**
     * @return void
     */
    abstract protected function parseContent();
    
    /**
     * @return mixed
     */
    public function getSleepDuration() {
        return $this->sleepDuration;
    }
    
    /**
     * @return mixed
     */
    public function getHeartbeatInterval() {
        return $this->heartbeatInterval;
    }
    
    /**
     * @return mixed
     */
    public function getHeartbeatTimeout() {
        return $this->heartbeatTimeout;
    }
    
    /**
     * @return mixed
     */
    public function getHeartbeatContent() {
        return $this->heartbeatContent;
    }
    
    /**
     * @return mixed
     */
    public function getHeartbeatEOL() {
        return $this->heartbeatEOL;
    }
    
    /**
     * @return mixed
     */
    public function __toString() {
        return sprintf('%s: %d', __CLASS__, time());
    }
}
