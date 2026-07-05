<?php
declare(strict_types = 1);

namespace Slothsoft\Core\DBMS;

/**
 * Legacy DBMS table wrapper for select, insert, update, delete, and schema operations.
 *
 * @author Daniel Schulz
 * @since 2018-03-29
 */
final class Table {
    
    protected Database $db;
    
    protected string $name;
    
    /**
     * @param Database $db
     * @param string $tableName
     */
    public function __construct(Database $db, string $tableName) {
        $this->db = $db;
        $this->name = $tableName;
    }
    
    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @return ?bool
     */
    public function tableExists(): ?bool {
        return $this->db->tableExists($this->name);
    }
    
    /**
     * @param string $newTableName
     * @param ?string $newDbName
     * @return bool
     */
    public function tableMove(string $newTableName, ?string $newDbName = null): bool {
        return $this->db->tableMove($this->name, $newTableName, $newDbName);
    }
    
    // CREATE DATABASE
    
    /**
     * @param array $cols
     * @param array $keys
     * @param array $options
     * @return void
     */
    public function createTable(array $cols, array $keys, array $options = []): void {
        $this->db->createTable($this->name, $cols, $keys, $options);
    }
    
    /**
     * @param array|string|bool $columnQuery
     * @param array|string $sqlQuery
     * @param string $sqlSuffix
     * @return ?array
     */
    public function select($columnQuery = true, $sqlQuery = '', string $sqlSuffix = ''): ?array {
        return $this->db->select($this->name, $columnQuery, $sqlQuery, $sqlSuffix);
    }
    
    /**
     * @param array $insertData
     * @param array $onDuplicateData
     * @return ?int
     */
    public function insert(array $insertData = [], array $onDuplicateData = []): ?int {
        return $this->db->insert($this->name, $insertData, $onDuplicateData);
    }
    
    /**
     * @param array $arr
     * @param mixed $idQuery
     * @return ?int
     */
    public function update(array $arr = [], $idQuery = false): ?int {
        return $this->db->update($this->name, $arr, $idQuery);
    }
    
    /**
     * @param mixed $idQuery
     * @return ?int
     */
    public function delete($idQuery = false): ?int {
        return $this->db->delete($this->name, $idQuery);
    }
    
    /**
     * @return ?array
     */
    public function getColumns(): ?array {
        return $this->db->getColumns($this->name);
    }
    
    /**
     * @return ?bool
     */
    public function optimize(): ?bool {
        return $this->db->optimize($this->name);
    }
    
    /**
     * @param string $string
     * @return string
     */
    public function escape(string $string): string {
        return $this->db->escape($string);
    }
    
    /**
     * @param array|string $index
     * @return void
     */
    public function addIndex($index): void {
        $this->db->addIndex($this->name, $index);
    }
}
