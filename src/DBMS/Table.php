<?php
declare(strict_types = 1);

namespace Slothsoft\Core\DBMS;

/**
 * Legacy DBMS table wrapper for select, insert, update, delete, and schema operations.
 *
 * @author Daniel Schulz
 * @since 2018-03-29
 * @deprecated Included for historical compatibility only. The DBMS API is out of support and should not be used in new code.
 */
final class Table {
    
    protected Database $db;
    
    protected string $name;
    
    /**
     * @param Database $db
     * @param string $tableName
     */
    public function __construct(Database $db, $tableName) {
        $this->db = $db;
        $this->name = $tableName;
    }
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @return bool|null
     */
    public function tableExists(): ?bool {
        return $this->db->tableExists($this->name);
    }
    
    /**
     * @param string $newTableName
     * @param string|null $newDbName
     * @return bool
     */
    public function tableMove($newTableName, $newDbName = null) {
        return $this->db->tableMove($this->name, $newTableName, $newDbName);
    }
    
    // CREATE DATABASE
    
    /**
     * @param array $cols
     * @param array $keys
     * @param array $options
     * @return void
     */
    public function createTable(array $cols, array $keys, array $options = []) {
        $this->db->createTable($this->name, $cols, $keys, $options);
    }
    
    /**
     * @param mixed $cols
     * @param string $sqlString
     * @param string $sqlSuffix
     * @return array|null
     */
    public function select($cols = true, $sqlString = '', $sqlSuffix = '') {
        return $this->db->select($this->name, $cols, $sqlString, $sqlSuffix);
    }
    
    /**
     * @param array $insertData
     * @param array $onDuplicateData
     * @return int|null
     */
    public function insert($insertData = [], $onDuplicateData = []) {
        return $this->db->insert($this->name, $insertData, $onDuplicateData);
    }
    
    /**
     * @param array $arr
     * @param mixed $id
     * @return int|null
     */
    public function update($arr = [], $id = false) {
        return $this->db->update($this->name, $arr, $id);
    }
    
    /**
     * @param mixed $id
     * @return int|null
     */
    public function delete($id = false) {
        return $this->db->delete($this->name, $id);
    }
    
    /**
     * @return array|null
     */
    public function getColumns(): ?array {
        return $this->db->getColumns($this->name);
    }
    
    /**
     * @return bool|null
     */
    public function optimize(): ?bool {
        return $this->db->optimize($this->name);
    }
    
    /**
     * @param string $string
     * @return string
     */
    public function escape($string) {
        return $this->db->escape($string);
    }
    
    /**
     * @param array|string $index
     * @return void
     */
    public function addIndex($index) {
        $this->db->addIndex($this->name, $index);
    }
}
