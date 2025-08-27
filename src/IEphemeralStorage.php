<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use DOMDocument;
use DOMNode;

/**
 * Defines a data repository with built-in decay.
 *
 * @author Daniel
 *
 */
interface IEphemeralStorage {

    public function exists(string $name, int $modifyTime): bool;

    public function retrieve(string $name, int $modifyTime): ?string;

    public function retrieveXML(string $name, int $modifyTime, DOMDocument $targetDoc = null): ?DOMNode;

    public function retrieveDocument(string $name, int $modifyTime): ?DOMDocument;

    public function retrieveJSON(string $name, int $modifyTime);

    public function delete(string $name): bool;

    public function store(string $name, string $payload, int $modifyTime): bool;

    public function storeXML(string $name, DOMNode $dataNode, int $modifyTime): bool;

    public function storeDocument(string $name, DOMDocument $dataDoc, int $modifyTime): bool;

    public function storeJSON(string $name, $dataObject, int $modifyTime): bool;
}