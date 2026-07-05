<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use DOMDocument;
use DOMXPath;
use Slothsoft\Core\IO\FileInfoFactory;

/**
 * Legacy Cloudflare challenge workaround for loading protected pages through XMLHttpRequest.
 *
 * @author Daniel Schulz
 * @since 2017-12-28
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
final class CloudFlareScraper {
    
    protected $cookieFile;
    
    protected $trySolving;
    
    protected $lastURI;
    
    /**
     * @param mixed $cookieFile
     * @param mixed $trySolving
     */
    public function __construct($cookieFile = null, $trySolving = true) {
        $this->cookieFile = $cookieFile === null ? FileInfoFactory::createTempFile() : $cookieFile;
        $this->trySolving = $trySolving;
    }
    
    /**
     * @param mixed $uri
     * @return ?string
     */
    public function getFile($uri): ?string {
        $req = $this->_httpRequest($uri);
        return $req->responseText;
    }
    
    /**
     * @param mixed $uri
     * @return ?DOMXPath
     */
    public function getXPath($uri): ?DOMXPath {
        $req = $this->_httpRequest($uri);
        return $req->responseXML ? $this->_loadXPath($req->responseXML) : null;
    }
    
    /**
     * @param mixed $uri
     * @return mixed
     */
    protected function _httpRequest($uri) {
        $req = new XMLHttpRequest();
        $req->open('GET', $uri);
        $req->setCookieFile($this->cookieFile);
        $req->setRequestHeader('referer', $this->lastURI ?: $uri);
        $req->send();
        
        if ($req->responseXML) {
            $this->lastURI = $uri;
            
            if ($this->_isProtected($req->responseText)) {
                if ($this->trySolving) {
                    $this->trySolving = false;
                    $req = $this->_solveChallenge($uri, $req->responseXML, $req->responseText);
                } else {
                    $req->responseXML = null;
                }
            }
        }
        
        return $req;
    }
    
    /**
     * @param mixed $requestURI
     * @param DOMDocument $htmlDoc
     * @param mixed $html
     * @return void
     */
    protected function _solveChallenge($requestURI, DOMDocument $htmlDoc, $html) {
        $ret = null;
        $translationTable = [
            '!+[]' => '1',
            '!![]' => '1',
            '![]' => '0',
            '[]' => '0',
            ')+(' => ').('
        ];
        $match = [];
        if (preg_match('/f, (\w+)={"(\w+)":([^}]+)};/', $html, $match)) {
            // my_dump($match);
            $var = 0;
            $code = sprintf('$var+=%s;', $match[3]);
            $code = strtr($code, $translationTable);
            
            eval($code);
            
            // hLpaLqb.rkJvDPgfESVZ+=+((!+[]+!![]+!![]+!![]+!![]+[])+(+[]));
            $varName = $match[1] . '.' . $match[2];
            $query = sprintf('/%s\.%s.=([^;]+);/', $match[1], $match[2]);
            $matchList = [];
            if (preg_match_all($query, $html, $matchList)) {
                // my_dump($matchList);
                $translationTable[$varName] = '$var';
                
                // my_dump($var);
                foreach ($matchList[0] as $code) {
                    $code = strtr($code, $translationTable);
                    
                    // echo $code . PHP_EOL;
                    eval($code);
                    // my_dump($var);
                }
                
                // my_dump($var);
                
                $var += strlen(parse_url($requestURI, PHP_URL_HOST));
                
                if ($xpath = $this->_loadXPath($htmlDoc)) {
                    $path = $xpath->evaluate('string(//form/@action)');
                    $data = [];
                    $data['jschl_vc'] = $xpath->evaluate('string(//input[@name = "jschl_vc"]/@value)');
                    $data['pass'] = $xpath->evaluate('string(//input[@name = "pass"]/@value)');
                    $data['jschl_answer'] = (string) $var;
                    
                    $uri = sprintf('%s://%s%s?%s', parse_url($requestURI, PHP_URL_SCHEME), parse_url($requestURI, PHP_URL_HOST), $path, http_build_query($data));
                    // echo $uri . PHP_EOL;
                    // echo file_get_contents($options['cookieFile']) . PHP_EOL;
                    // my_dump($options);
                    sleep(5);
                    
                    $ret = $this->_httpRequest($uri);
                }
            }
        }
        return $ret;
    }
    
    /**
     * @param mixed $html
     * @return bool
     */
    protected function _isProtected($html): bool {
        return (bool) strpos($html, 's,t,o,p,b,r,e,a,k,i,n,g');
    }
    
    /**
     * @param DOMDocument $doc
     * @return DOMXPath
     */
    protected function _loadXPath(DOMDocument $doc): DOMXPath {
        return DOMHelper::loadXPath($doc);
    }
    
    /**
     * @return array
     */
    public function __serialize(): array {
        return [
            'cookieFile' => $this->cookieFile,
            'trySolving' => $this->trySolving
        ];
    }
    
    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data) {
        $this->__construct($data['cookieFile'], $data['trySolving']);
    }
}
