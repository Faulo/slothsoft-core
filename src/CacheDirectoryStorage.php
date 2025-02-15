<?php
declare(strict_types = 1);
/**
 * *********************************************************************
 * \Storage v1.01 01.09.2015 © Daniel Schulz
 *
 * Changelog:
 * v1.01 01.09.2015
 * $req->followRedirects = (int) (bool) $options['followRedirects'];
 * v1.00 25.07.2014
 * initial release
 * *********************************************************************
 */
namespace Slothsoft\Core;

use Slothsoft\Core\Calendar\DateTimeFormatter;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\Configuration\ConfigurationField;
use Slothsoft\Core\Configuration\DirectoryConfigurationField;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Core\DBMS\Manager;
use Slothsoft\Core\DBMS\Table;
use DOMDocument;
use DOMNode;
use Exception;

class CacheDirectoryStorage implements IEphemeralStorage {

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
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $directory . DIRECTORY_SEPARATOR . $third;
    }

    public function __construct() {
        $this->rootDirectory = ServerEnvironment::getCacheDirectory() . DIRECTORY_SEPARATOR . self::ROOT;
    }

    public function install(): void {
        if (! is_dir($this->rootDirectory)) {
            mkdir($this->rootDirectory, 0777, true);
        }
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

    public function retrieveDocument(string $name, int $modifyTime): ?DOMDocument {}

    public function retrieveJSON(string $name, int $modifyTime) {}

    public function delete(string $name): bool {}

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

    public function storeDocument(string $name, DOMDocument $dataDoc, int $modifyTime): bool {}

    public function storeJSON(string $name, $dataObject, int $modifyTime): bool {}
}