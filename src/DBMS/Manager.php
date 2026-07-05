<?php
declare(strict_types = 1);

namespace Slothsoft\Core\DBMS;

use Slothsoft\Core\Calendar\DateTimeFormatter;
use Slothsoft\Core\Configuration\ConfigurationField;
use Slothsoft\Core\Configuration\DirectoryConfigurationField;
use Slothsoft\Core\ServerEnvironment;

/**
 * Legacy DBMS registry for databases, tables, connection clients, and query logging.
 *
 * @author Daniel Schulz
 * @since 2018-03-29
 * @deprecated Included for historical compatibility only. The DBMS API is out of support and should not be used in new code.
 */
final class Manager {
    
    private static function logEnabled(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField(false);
        }
        return $field;
    }
    
    /**
     * @param bool $value
     * @return void
     */
    public static function setLogEnabled(bool $value): void {
        self::logEnabled()->setValue($value);
    }
    
    /**
     * @return bool
     */
    public static function getLogEnabled(): bool {
        return self::logEnabled()->getValue();
    }
    
    private static function logDirectory(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(ServerEnvironment::getLogDirectory() . 'dbms');
        }
        return $field;
    }
    
    /**
     * @param string $directory
     * @return void
     */
    public static function setLogDirectory(string $directory): void {
        self::logDirectory()->setValue($directory);
    }
    
    /**
     * @return string
     */
    public static function getLogDirectory(): string {
        return self::logDirectory()->getValue();
    }
    
    const LOG_LINELENGTH = 120;
    
    protected static Client $client;
    
    protected static array $databaseList = [];
    
    protected static array $tableList = [];
    
    /**
     * @return Client
     */
    public static function getClient(): Client {
        if (! isset(self::$client)) {
            self::_createLog('Manager: creating Client...');
            self::$client = new Client();
        }
        return self::$client;
    }
    
    /**
     * @param string $dbName
     * @return Database
     */
    public static function getDatabase(string $dbName): Database {
        $dbName = mb_strtolower(trim($dbName));
        if (! isset(self::$databaseList[$dbName])) {
            self::_createLog(sprintf('Manager: creating Database %s...', $dbName));
            
            self::$databaseList[$dbName] = new Database(self::getClient(), $dbName);
        }
        return self::$databaseList[$dbName];
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @return Table
     */
    public static function getTable(string $dbName, string $tableName): Table {
        $dbName = mb_strtolower(trim($dbName));
        $tableName = mb_strtolower(trim($tableName));
        if (! isset(self::$tableList[$dbName])) {
            self::$tableList[$dbName] = [];
        }
        if (! isset(self::$tableList[$dbName][$tableName])) {
            self::_createLog(sprintf('Manager: creating Table %s.%s...', $dbName, $tableName));
            
            self::$tableList[$dbName][$tableName] = new Table(self::getDatabase($dbName), $tableName);
        }
        return self::$tableList[$dbName][$tableName];
    }
    
    /**
     * @return void
     */
    public static function cron(): void {
        $infoTable = self::getTable('information_schema', 'TABLES');
        $tableList = $infoTable->select([
            'TABLE_SCHEMA',
            'TABLE_NAME',
            'ENGINE'
        ], [
            'TABLE_TYPE' => 'BASE TABLE',
            'ENGINE' => [
                "InnoDB",
                "MyISAM"
            ]
        ]);
        foreach ($tableList as $table) {
            $dbName = $table['TABLE_SCHEMA'];
            $tableName = $table['TABLE_NAME'];
            $dataTable = self::getTable($dbName, $tableName);
            if ($dataTable->tableExists()) {
                echo sprintf('Optimizing %s.%s...', $dbName, $tableName);
                if ($dataTable->optimize()) {
                    echo 'OK!' . PHP_EOL;
                } else {
                    echo 'FAILURE?!?';
                    die();
                }
            }
        }
    }
    
    /**
     * @param string $sql
     * @return void
     */
    public static function _createLog(string $sql): void {
        if (self::getLogEnabled()) {
            if (strlen($sql) > self::LOG_LINELENGTH) {
                $sql = substr($sql, 0, self::LOG_LINELENGTH) . '...';
            }
            $log = sprintf('[%s] %s%s', date(DateTimeFormatter::FORMAT_DATETIME), $sql, PHP_EOL);
            if ($handle = fopen(self::getLogDirectory(), 'ab')) {
                fwrite($handle, $log);
                fclose($handle);
            }
        }
    }
}
