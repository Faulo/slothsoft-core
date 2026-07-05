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
     * @param string $dbName
     */
    public function __construct(Client $client, string $dbName) {
        $this->client = $client;
        $this->name = $dbName;
    }
    
    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @param string $tableName
     * @return ?bool
     */
    public function tableExists(string $tableName): ?bool {
        return $this->client->tableExists($this->name, $tableName);
    }
    
    /**
     * @param string $oldTableName
     * @param string $newTableName
     * @param ?string $newDbName
     * @return bool
     */
    public function tableMove(string $oldTableName, string $newTableName, ?string $newDbName = null): bool {
        if (! $newDbName) {
            $newDbName = $this->name;
        }
        return $this->client->tableMove($this->name, $oldTableName, $newDbName, $newTableName);
    }
    
    /**
     * @return ?bool
     */
    public function databaseExists(): ?bool {
        return $this->client->databaseExists($this->name);
    }
    
    /**
     * @return array
     */
    public function getTableList(): array {
        return $this->client->getTableList($this->name);
    }
    
    /**
     * @param string $tableName
     * @return Table
     */
    public function getTable(string $tableName): Table {
        return Manager::getTable($this->name, $tableName);
    }
    
    /**
     * @return void
     */
    public function createDatabase(): void {
        $this->client->createDatabase($this->name);
    }
    
    /**
     * @return void
     */
    public function deleteDatabase(): void {
        $this->client->deleteDatabase($this->name);
    }
    
    /**
     * @param string $tableName
     * @param array $cols
     * @param array $keys
     * @param array $options
     * @return void
     */
    public function createTable(string $tableName, array $cols, array $keys, array $options = []): void {
        $this->client->createTable($this->name, $tableName, $cols, $keys, $options);
    }
    
    /**
     * @param string $tableName
     * @param array|string|bool $columnQuery
     * @param array|string $sqlQuery
     * @param string $sqlSuffix
     * @return ?array
     */
    public function select(string $tableName, $columnQuery = true, $sqlQuery = '', string $sqlSuffix = ''): ?array {
        return $this->client->select($this->name, $tableName, $columnQuery, $sqlQuery, $sqlSuffix);
    }
    
    /**
     * @param string $tableName
     * @param array $insertData
     * @param array $onDuplicateData
     * @return ?int
     */
    public function insert(string $tableName, array $insertData = [], array $onDuplicateData = []): ?int {
        return $this->client->insert($this->name, $tableName, $insertData, $onDuplicateData);
    }
    
    /**
     * @param string $tableName
     * @param array $arr
     * @param mixed $idQuery
     * @return ?int
     */
    public function update(string $tableName, array $arr = [], $idQuery = false): ?int {
        return $this->client->update($this->name, $tableName, $arr, $idQuery);
    }
    
    /**
     * @param string $tableName
     * @param mixed $idQuery
     * @return ?int
     */
    public function delete(string $tableName, $idQuery = false): ?int {
        return $this->client->delete($this->name, $tableName, $idQuery);
    }
    
    /**
     * @param string $tableName
     * @return ?array
     */
    public function getColumns(string $tableName): ?array {
        return $this->client->getColumns($this->name, $tableName);
    }
    
    /**
     * @param ?string $tableName
     * @return ?bool
     */
    public function optimize(?string $tableName = null): ?bool {
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
    public function escape(string $string): string {
        return $this->client->escape($string);
    }
    
    /**
     * @param string $tableName
     * @param array|string $index
     * @return void
     */
    public function addIndex(string $tableName, $index): void {
        $this->client->addIndex($this->name, $tableName, $index);
    }
    
    /**
     * @return void
     */
    public function resetCharset(): void {
        $this->client->resetCharset($this->name);
        $tableList = $this->getTableList();
        foreach ($tableList as $table) {
            $table = Manager::getTable($this->name, $table);
            $this->client->resetCharset($this->name, $table->getName());
        }
    }
}
