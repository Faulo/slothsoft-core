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
    
    protected Client $client;
    
    protected string $name;
    
    /**
     * @param Client $client
     * @param mixed $dbName
     */
    public function __construct(Client $client, string $dbName) {
        $this->client = $client;
        $this->name = $dbName;
    }
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @param string $tableName
     * @return bool|null
     */
    public function tableExists(string $tableName): ?bool {
        return $this->client->tableExists($this->name, $tableName);
    }
    
    /**
     * @param string $oldTableName
     * @param string $newTableName
     * @param string $newDbName
     * @return bool
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
     * @return array
     */
    public function getTableList() {
        return $this->client->getTableList($this->name);
    }
    
    /**
     * @param string $tableName
     * @return Table
     */
    public function getTable($tableName) {
        return Manager::getTable($this->name, $tableName);
    }
    
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
    
    /**
     * @param string $tableName
     * @param array $cols
     * @param array $keys
     * @param array $options
     * @return void
     */
    public function createTable($tableName, array $cols, array $keys, array $options = []) {
        $this->client->createTable($this->name, $tableName, $cols, $keys, $options);
    }
    
    /**
     * @param string $tableName
     * @param mixed $cols
     * @param string $sqlString
     * @param string $sqlSuffix
     * @return array|null
     */
    public function select($tableName, $cols = true, $sqlString = '', $sqlSuffix = '') {
        return $this->client->select($this->name, $tableName, $cols, $sqlString, $sqlSuffix);
    }
    
    /**
     * @param string $tableName
     * @param array $insertData
     * @param array $onDuplicateData
     * @return int|null
     */
    public function insert($tableName, $insertData = [], $onDuplicateData = []) {
        return $this->client->insert($this->name, $tableName, $insertData, $onDuplicateData);
    }
    
    /**
     * @param string $tableName
     * @param array $arr
     * @param mixed $id
     * @return int|null
     */
    public function update($tableName, $arr = [], $id = false) {
        return $this->client->update($this->name, $tableName, $arr, $id);
    }
    
    /**
     * @param string $tableName
     * @param mixed $id
     * @return int|null
     */
    public function delete($tableName, $id = false) {
        return $this->client->delete($this->name, $tableName, $id);
    }
    
    /**
     * @param string $tableName
     * @return array|null
     */
    public function getColumns($tableName): ?array {
        return $this->client->getColumns($this->name, $tableName);
    }
    
    /**
     * @param string $tableName
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
     * @param string $string
     * @return string
     */
    public function escape($string) {
        return $this->client->escape($string);
    }
    
    /**
     * @param string $tableName
     * @param array|string $index
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
