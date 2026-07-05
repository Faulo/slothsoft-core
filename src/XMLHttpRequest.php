<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use BadMethodCallException;
use DOMDocument;
use Exception;
use Slothsoft\Core\Calendar\Seconds;
use w3c\XMLHttpRequestEventTarget;

/**
 * Browser-style HTTP client abstraction over cURL.
 *
 * Example:
 *
 * <code>
 * $req = new XMLHttpRequest();
 * $req->open($method, $uri);
 * $req->send($data);
 * $req->responseText;
 * </code>
 *
 * @author Daniel Schulz
 * @since 2012-10-19
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
final class XMLHttpRequest implements \w3c\XMLHttpRequest {
    
    const NEWLINE = "
";
    
    /*
     * public $onloadstart;
     * public $onprogress;
     * public $onabort;
     * public $onerror;
     * public $onload;
     * public $ontimeout;
     * public $onloadend;
     * public $onreadystatechange;
     */
    public int $readyState = self::UNSENT;
    
    public int $timeout = 0;
    
    public bool $withCredentials;
    
    public ?XMLHttpRequestEventTarget $upload;
    
    public int $status = 0;
    
    public string $statusText = '';
    
    public string $responseType;
    
    public $response = null;
    
    public ?string $responseText = null;
    
    public ?DOMDocument $responseXML = null;
    
    public int $httpVersion = CURL_HTTP_VERSION_1_1;
    
    public int $followRedirects = 1;
    
    public int $maxRedirects = 10;
    
    public int $connectTimeout = 300;
    
    public int $transferTimeout = Seconds::HOUR;
    
    public int $globalDNS = 0;
    
    public int $keepAliveActive = 1;
    
    public int $keepAliveTimeout = 10;
    
    public int $keepAliveInterval = 10;
    
    protected string $method;
    
    protected bool $ssl;
    
    protected string $url;
    
    protected array $urlParam;
    
    protected bool $async;
    
    protected ?string $user;
    
    protected ?string $password;
    
    protected array $requestHeaders;
    
    protected array $responseHeaders;
    
    protected array $eventListeners;
    
    protected string $responseHead;
    
    protected ?string $_responseType = null;
    
    protected ?string $_responseCharset = null;
    
    protected string $utf8BOM;
    
    protected string $cookieFile = '';
    
    public static array $cookies = array();
    
    public static bool $useCookies = false;
    
    protected array $_env = [
        'SERVER_NAME' => 'localhost',
        'SERVER_SOFTWARE' => 'PHP'
    ];
    
    /**
     * @return void
     */
    public function __construct() {
        if (isset($_SERVER)) {
            foreach ($this->_env as $key => &$val) {
                if (isset($_SERVER[$key])) {
                    $val = $_SERVER[$key];
                }
            }
            unset($val);
        }
    }
    
    // request
    /**
     * @param string $method
     * @param string $url
     * @param bool $async
     * @param string|null $user
     * @param string|null $password
     * @return void
     */
    public function open(string $method, string $url, bool $async = true, ?string $user = null, ?string $password = null): void {
        $this->method = $method;
        $this->url = $url;
        $this->async = $async;
        $this->user = $user;
        $this->password = $password;
        $this->requestHeaders = array();
        $this->responseHeaders = array();
        $this->eventListeners = array();
        
        $this->utf8BOM = pack('CCC', 239, 187, 191);
        
        $host = null;
        $this->urlParam = array(
            'scheme' => 'http',
            'host' => $host,
            'port' => null,
            'user' => null,
            'pass' => null,
            'path' => '/',
            'query' => null,
            'fragment' => null
        );
        $arr = parse_url($url);
        foreach (array_keys($this->urlParam) as $key) {
            if (isset($arr[$key])) {
                $this->urlParam[$key] = $arr[$key];
            }
        }
        
        $this->ssl = $this->urlParam['scheme'] === 'https';
        
        if (! $this->urlParam['port']) {
            $this->urlParam['port'] = $this->ssl ? 443 : 80;
        }
        
        /*
         * if (preg_match('/^(.+):\/\/(.+)$/', $url, $match)) {
         * $this->ssl = ($match[1] === 'https');
         * $url = $match[2];
         * if (preg_match('/^([^\/]+)(.*)$/', $url, $match)) {
         * $this->host = $match[1];
         * $this->url = $match[2];
         * } else {
         * $this->host = $url;
         * $this->url = '';
         * }
         * if (preg_match('/^(.+):(\d+)$/', $this->host, $match)) {
         * $this->host = $match[1];
         * $this->port = (int) $match[2];
         * }
         * } else {
         * $this->ssl = false;
         * $this->host = $_SERVER['HTTP_HOST'];
         * $this->url = $url;
         * }
         */
        $this->readyState = self::OPENED;
    }
    
    /**
     * @param string $header
     * @param string $value
     * @return void
     * @throws Exception
     */
    public function setRequestHeader(string $header, string $value): void {
        if ($this->readyState !== self::OPENED) {
            throw new Exception('InvalidStateError');
        }
        $this->requestHeaders[strtolower($header)] = $value;
    }
    
    /**
     * @param mixed $header
     * @return mixed
     */
    public function getRequestHeader($header) {
        $header = strtolower($header);
        return $this->requestHeaders[$header] ?? null;
    }
    
    /**
     * @param mixed $data
     * @return void
     * @throws Exception
     */
    public function send($data = null): void {
        $type = null;
        if ($data !== null) {
            if (is_string($data)) {
                $type = 'application/x-www-form-urlencoded';
            }
            if ($data instanceof DOMDocument) {
                $data = $data->saveXML();
                $type = 'application/xml';
            }
            if (is_array($data)) {
                $data = http_build_query($data);
                $type = 'application/x-www-form-urlencoded';
            }
        }
        $data = (string) $data;
        if ($tmp = $this->getRequestHeader('Content-Type')) {
            $type = $tmp;
        }
        
        $this->setRequestHeader('User-Agent', sprintf('Mozilla/5.0 (%s) %s (KHTML, like Gecko) %s', $this->_env['SERVER_NAME'], $this->_env['SERVER_SOFTWARE'], 'XMLHttpRequest/2.0'));
        $this->setRequestHeader('Connection', 'close');
        if ($type) {
            $this->setRequestHeader('Content-Type', $type);
            $this->setRequestHeader('Content-length', (string) strlen($data));
        }
        if (self::$useCookies and self::$cookies) {
            $cookies = array();
            foreach (self::$cookies as $key => $val) {
                $cookies[] = $key . '=' . $val;
            }
            $this->setRequestHeader('Cookie', implode('; ', $cookies));
        }
        
        $header = array();
        foreach ($this->requestHeaders as $key => $value) {
            $header[] .= $key . ': ' . $value;
        }
        // my_dump($this->requestHeaders);
        // my_dump($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        
        // curl_setopt($ch, CURLOPT_HTTP_VERSION, $this->httpVersion);
        
        switch ($this->method) {
            case 'HEAD':
                curl_setopt($ch, CURLOPT_NOBODY, true);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
                break;
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
                break;
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->transferTimeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followRedirects);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
        if ($this->globalDNS) {
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 1);
        }
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, $this->keepAliveActive);
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, $this->keepAliveTimeout);
        curl_setopt($ch, CURLOPT_TCP_KEEPINTVL, $this->keepAliveInterval);
        
        if ($this->cookieFile) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        }
        
        // Get the response and close the channel.
        $res = curl_exec($ch);
        if ($err = curl_error($ch)) {
            throw new Exception(__CLASS__ . 'Exception:' . PHP_EOL . $err . PHP_EOL . print_r(curl_getinfo($ch), true));
        }
        $bodyPos = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        if (PHP_VERSION_ID < 80000) {
            // Note: This function has no effect. Prior to PHP 8.0.0, this function was used to close the resource. 
            curl_close($ch);
        }
        
        if ($res !== false) {
            // $bodyPos = strpos($res, self::NEWLINE . self::NEWLINE);
            $this->responseHead = (string) substr($res, 0, $bodyPos - strlen(self::NEWLINE . self::NEWLINE));
            $this->responseText = (string) substr($res, $bodyPos);
            // my_dump($req);
            // my_dump($res);
            // my_dump($this->responseHead);
            // my_dump($this->responseText);
            while ($headPos = (int) strpos($this->responseHead, self::NEWLINE . self::NEWLINE)) {
                $this->responseHead = substr($this->responseHead, $headPos + strlen(self::NEWLINE . self::NEWLINE));
            }
            $match = [];
            if (preg_match('/^HTTP\/1\.1 (\d+) ([\w ]+)/', $this->responseHead, $match)) {
                $this->status = (int) $match[1];
                $this->statusText = $match[2];
            }
            $head = self::parseHeaderList($this->responseHead);
            foreach ($head as $key => $val) {
                if (self::$useCookies and $key === 'set-cookie') {
                    self::addCookie($val);
                } else {
                    $this->responseHeaders[$key] = $val;
                }
            }
            
            /*
             * Redirects are implemented by curl via followRedirects.
             * Legacy manual redirect handling removed from execution path.
             */
            
            // http://tools.ietf.org/html/rfc2616
            /*
             * Chunked transfer decoding is intentionally left to the HTTP layer.
             * Legacy manual decoding removed from execution path.
             */
            if (isset($this->responseHeaders['content-type'])) {
                $type = explode(';', $this->responseHeaders['content-type'], 2);
                $this->_responseType = trim(strtolower($type[0]));
                switch ($this->_responseType) {
                    case 'application/json':
                        $this->_responseCharset = 'UTF-8';
                        break;
                    default:
                        break;
                }
                if (isset($type[1])) {
                    if (preg_match('/charset\s*=([^;]+)/', $type[1], $match)) {
                        $this->_responseCharset = strtoupper(trim($match[1]));
                    }
                }
            }
            
            $toCharset = 'UTF-8';
            $fromCharset = $this->_responseCharset ?: strtoupper(mb_detect_encoding($this->responseText)); // 'ISO-8859-1'
            
            if (strlen($fromCharset) and $toCharset !== $fromCharset) {
                $this->responseText = mb_convert_encoding($this->responseText, $toCharset, $fromCharset);
            }
            
            if (substr($this->responseText, 0, 3) === $this->utf8BOM) {
                $this->responseText = substr($this->responseText, 3);
            }
            
            // parse HTML/XML response body
            try {
                $isXML = false;
                if (strpos($this->_responseType, 'html') !== false) {
                    $isXML = true;
                }
                if (strpos($this->_responseType, 'xml') !== false) {
                    $isXML = true;
                }
                if ($isXML) {
                    $xml = $this->responseText;
                    $xml = str_replace('&nbsp;', '&#160;', $xml);
                    $xml = str_replace('&', '&amp;', $xml);
                    $xml = preg_replace('/&amp;(#?[\w\d]+;)/i', '&${1}', $xml);
                    if (strlen($xml)) {
                        $this->responseXML = new DOMDocument('1.0', $toCharset);
                        $this->responseXML->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_PARSEHUGE);
                        // my_dump($this->responseXML->saveXML());
                        if (! $this->responseXML->documentElement) {
                            $html = mb_convert_encoding($this->responseText, $fromCharset, $toCharset);
                            // $html = preg_replace('/\<!--+.*?-+-\>/s', '', $html); //kommentare rausnehmen, wer braucht die schon
                            // nicht-konforme "--" aus kommentaren entfernen
                            $matchList = [];
                            if (preg_match_all('/<!--+(.*?)-+->/s', $html, $matchList)) {
                                foreach ($matchList[0] as $i => $key) {
                                    $html = str_replace($key, sprintf('<!--%s-->', str_replace('--', '', $matchList[1][$i])), $html);
                                }
                            }
                            $this->responseXML->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_PARSEHUGE | LIBXML_HTML_NODEFDTD);
                            // überflüssige <?xml>s rausnehmen... unschön...
                            $delNodes = [];
                            foreach ($this->responseXML->childNodes as $childNode) {
                                if ($childNode !== $this->responseXML->documentElement) {
                                    $delNodes[] = $childNode;
                                }
                            }
                            foreach ($delNodes as $delNode) {
                                $delNode->parentNode->removeChild($delNode);
                            }
                        }
                        $this->responseXML->encoding = $toCharset;
                    }
                }
            } catch (Exception $e) {
                $this->responseXML = null;
            }
        }
        
        $this->readyState = self::DONE;
    }
    
    /**
     * @return void
     */
    public function abort(): void {
    }
    
    /**
     * @param string $mime
     * @return void
     * @throws Exception
     */
    public function overrideMimeType(string $mime): void {
        if ($this->readyState === self::OPENED or $this->readyState === self::DONE) {
            throw new Exception('InvalidStateError');
        }
    }
    
    /**
     * @param string $header
     * @return string
     */
    public function getResponseHeader(string $header): string {
        $header = strtolower($header);
        return $this->responseHeaders[$header] ?? '';
    }
    
    /**
     * @return array
     */
    public function getAllResponseHeaders(): array {
        return $this->responseHeaders;
    }
    
    // EventTarget
    /**
     * @param string $type
     * @param callable $listener
     * @param bool $capture
     * @return void
     */
    public function addEventListener(string $type, callable $listener, bool $capture = false): void {
        if (! isset($this->eventListeners[$type])) {
            $this->eventListeners[$type] = array();
        }
        if (! in_array($listener, $this->eventListeners[$type], true)) {
            $this->eventListeners[$type][] = $listener;
        }
    }
    
    /**
     * @param string $type
     * @param callable $listener
     * @param bool $capture
     * @return void
     */
    public function removeEventListener(string $type, callable $listener, bool $capture = false): void {
        if (! isset($this->eventListeners[$type])) {
            $this->eventListeners[$type] = array();
        }
        foreach ($this->eventListeners[$type] as $i => $tmp) {
            if ($tmp === $listener) {
                unset($this->eventListeners[$type][$i]);
                break;
            }
        }
    }
    
    /**
     * @param mixed $event
     * @return bool
     * @throws BadMethodCallException
     */
    public function dispatchEvent($event): bool {
        throw new BadMethodCallException("dispatchEvent is not implemented.");
    }
    
    // proprietary
    /**
     * @param string $file
     * @return void
     */
    public function setCookieFile(string $file): void {
        $this->cookieFile = $file;
    }
    
    /**
     * @return string
     */
    public function getCookieFile(): string {
        return $this->cookieFile;
    }
    
    // protected
    /**
     * @param mixed $type
     * @return void
     */
    protected function fireEventListener($type) {
        if (isset($this->eventListeners[$type])) {
            foreach ($this->eventListeners[$type] as $listener) {
                if (is_callable($listener)) {
                    call_user_func_array($listener, array());
                }
            }
        }
    }
    
    /**
     * @param mixed $line
     * @return void
     */
    protected static function addCookie($line) {
        $cookie = explode(';', $line, 2);
        $cookie = explode('=', $cookie[0], 2);
        self::$cookies[trim($cookie[0])] = $cookie[1];
    }
    
    /**
     * @param mixed $key
     * @param mixed $val
     * @return void
     */
    public static function setCookie($key, $val) {
        self::$cookies[$key] = $val;
    }
    
    /**
     * @param mixed $head
     * @return array
     */
    public static function parseHeaderList($head): array {
        $ret = [];
        $headList = explode("\n", str_replace("\r", "\n", $head));
        foreach ($headList as $line) {
            if (strlen($line)) {
                $line = explode(':', $line, 2);
                if (count($line) === 2) {
                    $key = trim(strtolower($line[0]));
                    $val = trim($line[1]);
                    if (strlen($key)) {
                        $ret[$key] = $val;
                    }
                }
            }
        }
        return $ret;
    }
}
