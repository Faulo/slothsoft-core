<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use DOMDocument;
use DOMNode;

/**
 * Storage backend for named payloads that expire by modification time.
 *
 * @author Daniel Schulz
 * @since 2025-02-15
 */
interface EphemeralStorageInterface {
    
    /**
     * @param string $name
     * @param int $modifyTime
     * @return bool
     */
    public function exists(string $name, int $modifyTime): bool;
    
    /**
     * @param string $name
     * @param int $modifyTime
     * @return string|null
     */
    public function retrieve(string $name, int $modifyTime): ?string;
    
    /**
     * @param string $name
     * @param int $modifyTime
     * @param DOMDocument|null $targetDoc
     * @return DOMNode|null
     */
    public function retrieveXML(string $name, int $modifyTime, ?DOMDocument $targetDoc = null): ?DOMNode;
    
    /**
     * @param string $name
     * @param int $modifyTime
     * @return DOMDocument|null
     */
    public function retrieveDocument(string $name, int $modifyTime): ?DOMDocument;
    
    /**
     * @param string $name
     * @param int $modifyTime
     * @return mixed
     */
    public function retrieveJSON(string $name, int $modifyTime);
    
    /**
     * @param string $name
     * @return bool
     */
    public function delete(string $name): bool;
    
    /**
     * @param string $name
     * @param string $payload
     * @param int $modifyTime
     * @return bool
     */
    public function store(string $name, string $payload, int $modifyTime): bool;
    
    /**
     * @param string $name
     * @param DOMNode $dataNode
     * @param int $modifyTime
     * @return bool
     */
    public function storeXML(string $name, DOMNode $dataNode, int $modifyTime): bool;
    
    /**
     * @param string $name
     * @param DOMDocument $dataDoc
     * @param int $modifyTime
     * @return bool
     */
    public function storeDocument(string $name, DOMDocument $dataDoc, int $modifyTime): bool;
    
    /**
     * @param string $name
     * @param mixed $dataObject
     * @param int $modifyTime
     * @return bool
     */
    public function storeJSON(string $name, $dataObject, int $modifyTime): bool;
}
