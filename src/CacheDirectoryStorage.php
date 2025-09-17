<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use DOMDocument;
use DOMNode;

class CacheDirectoryStorage implements EphemeralStorageInterface {
    
    protected static DOMHelper $dom;
    
    protected static function _DOMHelper() {
        if (! isset(self::$dom)) {
            self::$dom = new DOMHelper();
        }
        return self::$dom;
    }
    
    private const ROOT = 'storage';
    
    private string $rootDirectory;
    
    private function hashPath(string $name): string {
        $hash = sha1($name);
        
        $first = substr($hash, 0, 2);
        $second = substr($hash, 2, 2);
        $third = substr($hash, 4);
        
        $directory = $this->rootDirectory . DIRECTORY_SEPARATOR . $first . DIRECTORY_SEPARATOR . $second;
        
        FileSystem::ensureDirectory($directory);
        
        return $directory . DIRECTORY_SEPARATOR . $third;
    }
    
    public function __construct(string $name = '') {
        $this->rootDirectory = ServerEnvironment::getCacheDirectory() . DIRECTORY_SEPARATOR . self::ROOT;
        
        if (strlen($name)) {
            $this->rootDirectory .= DIRECTORY_SEPARATOR . FileSystem::filenameSanitize($name);
        }
        
        $this->install();
    }
    
    private function install(): void {
        FileSystem::ensureDirectory($this->rootDirectory);
    }
    
    public function exists(string $name, int $modifyTime): bool {
        $path = $this->hashPath($name);
        return is_file($path) and FileSystem::changetime($path) >= $modifyTime;
    }
    
    public function retrieve(string $name, int $modifyTime): ?string {
        $path = $this->hashPath($name);
        if (! is_file($path)) {
            return null;
        }
        
        if (FileSystem::changetime($path) < $modifyTime) {
            return null;
        }
        
        return file_get_contents($path);
    }
    
    public function retrieveXML(string $name, int $modifyTime, DOMDocument $targetDoc = null): ?DOMNode {
        $ret = null;
        if ($data = $this->retrieve($name, $modifyTime)) {
            $dom = self::_DOMHelper();
            $ret = $dom->parse($data, $targetDoc);
        }
        return $ret;
    }
    
    public function retrieveDocument(string $name, int $modifyTime): ?DOMDocument {
        $path = $this->hashPath($name);
        if (! is_file($path)) {
            return null;
        }
        
        if (FileSystem::changetime($path) < $modifyTime) {
            return null;
        }
        
        $document = new DOMDocument('1.0', 'UTF-8');
        
        if ($document->load($path, LIBXML_PARSEHUGE) and $document->documentElement) {
            return $document;
        } else {
            $this->delete($name);
            return null;
        }
    }
    
    public function retrieveJSON(string $name, int $modifyTime) {
        $retObject = null;
        $data = $this->retrieve($name, $modifyTime);
        if ($data !== null) {
            @$retObject = json_decode($data, true);
            if ($retObject === null) {
                $this->delete($name);
            }
        }
        return $retObject;
    }
    
    public function delete(string $name): bool {
        $path = $this->hashPath($name);
        $result = unlink($path);
        clearstatcache(true, $path);
        return $result;
    }
    
    public function store(string $name, string $payload, int $modifyTime): bool {
        $path = $this->hashPath($name);
        
        file_put_contents($path, $payload);
        
        if (! is_file($path)) {
            return false;
        }
        
        touch($path, $modifyTime);
        
        return true;
    }
    
    public function storeXML(string $name, DOMNode $dataNode, int $modifyTime): bool {
        $dom = self::_DOMHelper();
        return $this->store($name, $dom->stringify($dataNode), $modifyTime);
    }
    
    public function storeDocument(string $name, DOMDocument $dataDoc, int $modifyTime): bool {
        if (! $dataDoc->documentElement) {
            return false;
        }
        
        $path = $this->hashPath($name);
        if (! $dataDoc->save($path)) {
            return false;
        }
        
        touch($path, $modifyTime);
        
        return true;
    }
    
    public function storeJSON(string $name, $dataObject, int $modifyTime): bool {
        return $this->store($name, json_encode($dataObject), $modifyTime);
    }
}