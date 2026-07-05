<?php
declare(strict_types = 1);

namespace Slothsoft\Core\DBMS;

/**
 * Legacy DBMS database wrapper for table lookup and schema operations.
 *
 * @author Daniel Schulz
 * @since 2018-03-29
 * @deprecated Included for historical compatibility only. The DBMS API is out of support and should not be used in new code.
 */
final class Database {
    
    protected $client;
    
    protected $name;
    
    /**
     * @param Client $client
     * @param mixed $dbName
     * @return void
     */
    public function __construct(Client $client, $dbName) {
        $this->client = $client;
        $this->name = $dbName;
        // $this->client->setDatabase($this->name);
    }
    
    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @param mixed $tableName
     * @return bool|null
     */
    public function tableExists($tableName): ?bool {
        return $this->client->tableExists($this->name, $tableName);
    }
    
    /**
     * @param mixed $oldTableName
     * @param mixed $newTableName
     * @param mixed $newDbName
     * @return mixed
     */
    public function tableMove($oldTableName, $newTableName, $newDbName = null) {
        if (! $newDbName) {
            $newDbName = $this->name;
        }
        return $this->client->tableMove($this->name, $oldTableName, $newDbName, $newTableName);
    }
    
    /**
     * @return bool|null
     */
    public function databaseExists(): ?bool {
        return $this->client->databaseExists($this->name);
    }
    
    /**
     * @return mixed
     */
    public function getTableList() {
        return $this->client->getTableList($this->name);
    }
    
    /**
     * @param mixed $tableName
     * @return mixed
     */
    public function getTable($tableName) {
        return Manager::getTable($this->name, $tableName);
    }
    
    // CREATE DATABASE
    
    /**
     * @return void
     */
    public function createDatabase() {
        $this->client->createDatabase($this->name);
    }
    
    /**
     * @return void
     */
    public function deleteDatabase() {
        $this->client->deleteDatabase($this->name);
    }
    
    // CREATE TABLE
    
    /**
     * @param mixed $tableName
     * @param array $cols
     * @param array $keys
     * @param array $options
     * @return void
     */
    public function createTable($tableName, array $cols, array $keys, array $options = []) {
        $this->client->createTable($this->name, $tableName, $cols, $keys, $options);
    }
    
    // SELECT $cols FROM $table WHERE ($string)
    // $cols: true => ['*'], 'col' => 'col', ['c1', 'c2'] => 'c1, c2'
    /**
     * @param mixed $tableName
     * @param mixed $cols
     * @param mixed $sqlString
     * @param mixed $sqlSuffix
     * @return mixed
     */
    public function select($tableName, $cols = true, $sqlString = '', $sqlSuffix = '') {
        return $this->client->select($this->name, $tableName, $cols, $sqlString, $sqlSuffix);
    }
    
    // INSERT INTO $table ($arr[key]) VALUES ($arr[val])
    
    /**
     * @param mixed $tableName
     * @param mixed $insertData
     * @param mixed $onDuplicateData
     * @return mixed
     */
    public function insert($tableName, $insertData = [], $onDuplicateData = []) {
        return $this->client->insert($this->name, $tableName, $insertData, $onDuplicateData);
    }
    
    // UPDATE $table SET ($arr[key] = $arr[val]) WHERE id = $id
    
    /**
     * @param mixed $tableName
     * @param mixed $arr
     * @param mixed $id
     * @return mixed
     */
    public function update($tableName, $arr = [], $id = false) {
        return $this->client->update($this->name, $tableName, $arr, $id);
    }
    
    // DELETE FROM $table WHERE id = $id
    
    /**
     * @param mixed $tableName
     * @param mixed $id
     * @return mixed
     */
    public function delete($tableName, $id = false) {
        return $this->client->delete($this->name, $tableName, $id);
    }
    
    // SHOW COLUMNS
    
    /**
     * @param mixed $tableName
     * @return array|null
     */
    public function getColumns($tableName): ?array {
        return $this->client->getColumns($this->name, $tableName);
    }
    
    /**
     * @param mixed $tableName
     * @return bool|null
     */
    public function optimize($tableName = null): ?bool {
        if ($tableName) {
            return $this->client->optimize($this->name, $tableName);
        } else {
            $tableNameList = $this->getTableList();
            foreach ($tableNameList as $tableName) {
                $this->client->optimize($this->name, $tableName);
            }
        }
        return null;
    }
    
    /**
     * @param mixed $string
     * @return mixed
     */
    public function escape($string) {
        return $this->client->escape($string);
    }
    
    /**
     * @param mixed $tableName
     * @param mixed $index
     * @return void
     */
    public function addIndex($tableName, $index) {
        $this->client->addIndex($this->name, $tableName, $index);
    }
    
    /**
     * @return void
     */
    public function resetCharset() {
        $this->client->resetCharset($this->name);
        $tableList = $this->getTableList();
        foreach ($tableList as $table) {
            $table = Manager::getTable($this->name, $table);
            $this->client->resetCharset($this->name, $table->getName());
        }
    }
}
