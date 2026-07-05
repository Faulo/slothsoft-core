<?php
declare(strict_types = 1);

namespace Slothsoft\Core\DBMS;

use mysqli;
use mysqli_result;
use Slothsoft\Core\Configuration\ConfigurationField;
use Slothsoft\Core\Configuration\ConfigurationRequiredException;

/**
 * Legacy mysqli client wrapper with process-wide default connection configuration.
 *
 * @author Daniel Schulz
 * @since 2018-03-29
 */
final class Client {
    
    private static function getEnvWithDefault(string $envKey, string $defaultValue, ?string $envFileKey = null): string {
        $value = getenv($envKey);
        if ($value === false and $envFileKey and $file = self::getEnvWithDefault($envFileKey, '') and is_file($file)) {
            $value = file_get_contents($file);
        }
        if ($value === false) {
            $value = $defaultValue;
        }
        return $value;
    }
    
    private static function defaultAuthority(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField();
        }
        
        if (! $field->hasValue()) {
            $server = self::getEnvWithDefault(self::ENV_CONNECTION_SERVER, self::CONNECTION_SERVER_DEFAULT);
            $user = self::getEnvWithDefault(self::ENV_CONNECTION_USER, self::CONNECTION_SERVER_USER);
            $password = self::getEnvWithDefault(self::ENV_CONNECTION_PASSWORD, '', self::ENV_CONNECTION_PASSWORD_FILE);
            if ($server !== '' and $user !== '' and $password !== '') {
                $field->setValue(new Authority($server, $user, $password));
            }
        }
        
        return $field;
    }
    
    /**
     * @param Authority $authority
     * @return void
     */
    public static function setDefaultAuthority(Authority $authority): void {
        self::defaultAuthority()->setValue($authority);
    }
    
    /**
     * @return Authority
     */
    public static function getDefaultAuthority(): Authority {
        return self::defaultAuthority()->getValue();
    }
    
    /**
     * @return void
     */
    public static function clearDefaultAuthority(): void {
        self::defaultAuthority()->setValue(null);
    }
    
    public const ENV_CONNECTION_SERVER = 'MYSQL_HOST';
    
    private const CONNECTION_SERVER_DEFAULT = 'localhost';
    
    public const ENV_CONNECTION_USER = 'MYSQL_USER';
    
    private const CONNECTION_SERVER_USER = 'root';
    
    public const ENV_CONNECTION_PASSWORD = 'MYSQL_PASSWORD';
    
    public const ENV_CONNECTION_PASSWORD_FILE = 'MYSQL_PASSWORD_FILE';
    
    private const CONNECTION_CHARSET = 'utf8mb4';
    
    private const CONNECTION_COLLATION = 'utf8mb4_unicode_ci';
    
    protected mysqli $sqli;
    
    protected bool $connected = false;
    
    protected ?string $dbName = null;
    
    /**
     */
    public function __construct() {
    }
    
    /**
     * @return bool
     */
    public function reconnect(): bool {
        try {
            $authority = self::getDefaultAuthority();
        } catch (ConfigurationRequiredException $e) {
            $this->error('Database configuration has not been set!');
            return false;
        }
        
        $sqli = mysqli_init();
        if (! $sqli instanceof mysqli) {
            $this->error('Failed to initialize mysqli.');
            return false;
        }
        
        $this->sqli = $sqli;
        $this->sqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 1);
        $this->connected = false;
        
        if (! @$this->sqli->real_connect($authority->server, $authority->user, $authority->password)) {
            $this->error();
            return false;
        }
        
        if ($this->sqli->connect_error) {
            $this->error();
            return false;
        }
        if (! $this->sqli->set_charset(self::CONNECTION_CHARSET)) {
            $this->error('Failed to set connection charset.');
            return false;
        }
        if ($this->dbName and ! $this->sqli->select_db($this->dbName)) {
            $this->error(sprintf('Failed to select database "%s".', $this->dbName));
            return false;
        }
        return true;
    }
    
    /**
     * @return void
     */
    public function disconnect(): void {
        if ($this->connected) {
            $this->connected = false;
            $this->sqli->close();
        }
    }
    
    /**
     * @return bool
     */
    protected function connect(): bool {
        return ($this->connected and $this->sqli->ping()) or ($this->reconnect() and $this->connected = true);
    }
    
    /**
     * @param string $dbName
     * @return ?bool
     */
    public function setDatabase(string $dbName): ?bool {
        $ret = null;
        $this->dbName = null;
        if ($this->connect()) {
            $ret = $this->sqli->select_db($dbName);
            $this->dbName = $dbName;
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @return ?bool
     */
    public function tableExists(string $dbName, string $tableName): ?bool {
        $ret = null;
        if ($this->connect()) {
            $ret = $this->select('information_schema', 'tables', 'table_name', sprintf('table_schema = "%s" AND table_name = "%s"', $this->escape($dbName), $this->escape($tableName)));
            $ret = (bool) count($ret ?? []);
        }
        return $ret;
    }
    
    /**
     * @param string $oldDbName
     * @param string $oldTableName
     * @param string $newDbName
     * @param string $newTableName
     * @return bool
     */
    public function tableMove(string $oldDbName, string $oldTableName, string $newDbName, string $newTableName): bool {
        $oldHandle = $this->get_handle($oldDbName, $oldTableName);
        $newHandle = $this->get_handle($newDbName, $newTableName);
        $sql = sprintf('RENAME TABLE %s TO %s', $oldHandle, $newHandle);
        return (bool) $this->execute($sql);
    }
    
    /**
     * @param string $dbName
     * @return ?bool
     */
    public function databaseExists(string $dbName): ?bool {
        $ret = null;
        if ($this->connect()) {
            $ret = $this->select('information_schema', 'schemata', 'schema_name', sprintf('schema_name = "%s"', $this->escape($dbName)));
            $ret = (bool) count($ret ?? []);
        }
        return $ret;
    }
    
    /**
     * @return ?array
     */
    public function getDatabaseList(): ?array {
        $ret = null;
        if ($this->connect()) {
            $ret = $this->select('information_schema', 'schemata', 'schema_name');
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @return ?array
     */
    public function getTableList(string $dbName): ?array {
        $ret = null;
        if ($this->connect()) {
            $ret = $this->select('information_schema', 'tables', 'table_name', sprintf('table_schema = "%s"', $this->escape($dbName)));
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @return void
     */
    public function createDatabase(string $dbName): void {
        $dbHandle = $this->get_handle($dbName);
        $sql = sprintf('CREATE DATABASE IF NOT EXISTS %s', $dbHandle);
        if (! $this->execute($sql)) {
            $this->error($sql);
        }
    }
    
    /**
     * @param string $dbName
     * @return void
     */
    public function deleteDatabase(string $dbName): void {
        $dbHandle = $this->get_handle($dbName);
        $sql = sprintf('DROP DATABASE IF EXISTS %s', $dbHandle);
        if (! $this->execute($sql)) {
            $this->error($sql);
        }
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @param array $cols
     * @param array $keys
     * @param array $options
     * @return void
     */
    public function createTable(string $dbName, string $tableName, array $cols, array $keys, array $options = []): void {
        $dbHandle = $this->get_handle($dbName, $tableName);
        $colStr = [];
        foreach ($cols as $key => $val) {
            $colStr[] = sprintf('`%s` %s', $key, $val);
        }
        $colStr = implode(', ', $colStr);
        $keyStr = [];
        foreach ($keys as $key => $val) {
            if ($key) {
                if (is_array($val)) {
                    if (! isset($val['name'])) {
                        $val['name'] = reset($val['columns']);
                    }
                    $sql = sprintf('%s `%s` (%s)', $val['type'], $val['name'], implode(',', $val['columns']));
                } else {
                    $sql = sprintf('KEY `%s` (`%s`)', $val, $val);
                }
            } else {
                $sql = sprintf('PRIMARY KEY (`%s`)', $val);
            }
            $keyStr[] = $sql;
        }
        $keyStr = implode(', ', $keyStr);
        $optStr = '';
        if (isset($options['engine'])) {
            $optStr .= sprintf('ENGINE = %s', $options['engine']);
        }
        $sql = sprintf('CREATE TABLE %s ( %s , %s ) %s', $dbHandle, $colStr, $keyStr, $optStr);
        if (! $this->execute($sql)) {
            $this->error($sql);
        }
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @param array|string $index
     * @return void
     */
    public function addIndex(string $dbName, string $tableName, $index): void {
        if (! is_array($index)) {
            $index = [
                'name' => $index,
                'columns' => [
                    $index
                ]
            ];
        }
        $dbHandle = $this->get_handle($dbName, $tableName);
        $sql = sprintf('CREATE INDEX %s ON %s (%s)', $index['name'], $dbHandle, implode(',', $index['columns']));
        $this->execute($sql);
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @param array|string|bool $columnQuery
     * @param array|string $sqlQuery
     * @param string $sqlSuffix
     * @return ?array
     */
    public function select(string $dbName, string $tableName, $columnQuery = true, $sqlQuery = '', string $sqlSuffix = ''): ?array {
        $ret = null;
        $dbHandle = $this->get_handle($dbName, $tableName);
        if ($this->connect()) {
            if ($columnQuery === true) {
                $columnQuery = [
                    '*'
                ];
            }
            $retArr = is_array($columnQuery);
            if (! $retArr) {
                $columnQuery = [
                    (string) $columnQuery
                ];
            }
            if (is_array($sqlQuery)) {
                $tmpArr = [];
                foreach ($sqlQuery as $key => $val) {
                    if ($val === null) {
                        $tmpArr[] = sprintf('`%s` IS NULL', $key);
                    } elseif (is_int($val)) {
                        $tmpArr[] = sprintf('`%s`=%d', $key, $val);
                    } elseif (is_array($val)) {
                        if (count($val)) {
                            foreach ($val as &$v) {
                                if (! is_int($v)) {
                                    $v = sprintf('"%s"', $this->escape($v));
                                }
                            }
                            unset($v);
                            $tmpArr[] = sprintf('`%s` IN (%s)', $key, implode(',', $val));
                        } else {
                            $tmpArr[] = '0';
                        }
                    } else {
                        $tmpArr[] = sprintf('`%s`="%s"', $key, $this->escape($val));
                    }
                }
                $sqlQuery = implode(' AND ', $tmpArr);
            }
            if (! strlen($sqlQuery)) {
                $sqlQuery = '1';
            }
            if (strlen($sqlSuffix)) {
                $sqlQuery .= ' ' . $sqlSuffix;
            }
            $sql = sprintf('SELECT %s FROM %s WHERE %s', implode(',', $columnQuery), $dbHandle, $sqlQuery);
            if ($res = $this->execute($sql)) {
                if ($retArr) {
                    $ret = $res->fetch_all(MYSQLI_ASSOC);
                } else {
                    $ret = [];
                    foreach ($res as $tmp) {
                        $ret[] = current($tmp);
                    }
                }
            } else {
                $this->error($sql);
            }
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @param array $insertData
     * @param array $onDuplicateData
     * @return ?int
     */
    public function insert(string $dbName, string $tableName, array $insertData = [], array $onDuplicateData = []): ?int {
        $ret = null;
        $dbHandle = $this->get_handle($dbName, $tableName);
        if ($this->connect()) {
            $keys = array_keys($insertData);
            foreach ($insertData as &$val) {
                if ($val === null) {
                    $val = 'NULL';
                } elseif (! is_int($val)) {
                    $val = sprintf('"%s"', $this->escape($val));
                }
            }
            unset($val);
            $onDuplicateSQL = '';
            if (count($onDuplicateData)) {
                $onDuplicateSQL = sprintf(' ON DUPLICATE KEY UPDATE %s', $this->_get_update_data($onDuplicateData));
            }
            $sql = sprintf('INSERT INTO %s (`%s`) VALUES (%s)%s', $dbHandle, implode('`,`', $keys), implode(',', $insertData), $onDuplicateSQL);
            if ($this->execute($sql)) {
                $ret = $this->sqli->insert_id;
            } else {
                $this->error($sql);
            }
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @param array $arr
     * @param mixed $idQuery
     * @return ?int
     */
    public function update(string $dbName, string $tableName, array $arr = [], $idQuery = false): ?int {
        $ret = null;
        $dbHandle = $this->get_handle($dbName, $tableName);
        if ($this->connect()) {
            $sql = sprintf('UPDATE %s SET %s WHERE %s', $dbHandle, $this->_get_update_data($arr), $this->get_ids($idQuery));
            if ($this->execute($sql)) {
                $ret = $this->sqli->affected_rows;
            } else {
                $this->error($sql);
            }
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @param mixed $idQuery
     * @return ?int
     */
    public function delete(string $dbName, string $tableName, $idQuery = false): ?int {
        $ret = null;
        $dbHandle = $this->get_handle($dbName, $tableName);
        if ($this->connect()) {
            $sql = sprintf('DELETE FROM %s WHERE %s', $dbHandle, $this->get_ids($idQuery));
            if ($this->execute($sql)) {
                $ret = $this->sqli->affected_rows;
            } else {
                $this->error($sql);
            }
        }
        return $ret;
    }
    
    /**
     * @param string $sqlString
     * @return mysqli_result|bool
     */
    public function execute(string $sqlString) {
        if ($this->connect()) {
            Manager::_createLog($sqlString);
            $ret = @$this->sqli->query($sqlString);
            if ($ret === false) {
                $this->error($sqlString);
            }
            return $ret;
        }
        return false;
    }
    
    /**
     * @param string $file
     * @return ?bool
     */
    public function executeFile(string $file): ?bool {
        if ($sql = file_get_contents($file)) {
            return $this->sqli->multi_query($sql);
        }
        return null;
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @return ?array
     */
    public function getColumns(string $dbName, string $tableName): ?array {
        $ret = null;
        $dbHandle = $this->get_handle($dbName, $tableName);
        if ($this->connect()) {
            $sql = sprintf('SHOW COLUMNS from %s', $dbHandle);
            if ($res = $this->execute($sql)) {
                $ret = [];
                if ($res->num_rows > 0) {
                    while ($tmp = $res->fetch_assoc()) {
                        $ret[] = $tmp;
                    }
                }
            } else {
                $this->error($sql);
            }
        }
        return $ret;
    }
    
    /**
     * @param string $dbName
     * @param string $tableName
     * @return bool
     */
    public function optimize(string $dbName, string $tableName): bool {
        $dbHandle = $this->get_handle($dbName, $tableName);
        $sql = sprintf('OPTIMIZE TABLE %s', $dbHandle);
        $res = $this->execute($sql);
        if ($res === false) {
            return false;
        }
        
        $ret = [];
        if ($res->num_rows > 0) {
            while ($tmp = $res->fetch_assoc()) {
                $ret[] = $tmp;
            }
        }
        $err = [];
        foreach ($ret as $arr) {
            if (in_array($arr['Msg_text'], [
                'OK',
                'Table is already up to date'
            ])) {
                return true;
            } else {
                $err[] = $arr['Msg_text'];
            }
        }
        $this->error(implode(PHP_EOL, $err));
        return false;
    }
    
    /**
     * @param string $dbName
     * @param ?string $tableName
     * @return void
     */
    public function resetCharset(string $dbName, ?string $tableName = null): void {
        $mode = null;
        if (strlen($dbName)) {
            $mode = 'db';
            if (strlen($tableName)) {
                $mode = 'table';
            }
        }
        switch ($mode) {
            case 'db':
                $sql = sprintf('ALTER DATABASE %s CHARACTER SET %s COLLATE %s', $this->get_handle(null, $dbName), self::CONNECTION_CHARSET, self::CONNECTION_COLLATION);
                $this->execute($sql);
                break;
            case 'table':
                $sql = sprintf('ALTER TABLE %s DEFAULT CHARACTER SET %s COLLATE %s', $this->get_handle($dbName, $tableName), self::CONNECTION_CHARSET, self::CONNECTION_COLLATION);
                $this->execute($sql);
                $sql = sprintf('ALTER TABLE %s CONVERT TO CHARACTER SET %s COLLATE %s', $this->get_handle($dbName, $tableName), self::CONNECTION_CHARSET, self::CONNECTION_COLLATION);
                $this->execute($sql);
                break;
        }
    }
    
    /**
     * @param string $string
     * @return string
     */
    public function escape(string $string): string {
        if ($this->connect()) {
            return $this->sqli->real_escape_string($string);
        }
        return $string;
    }
    
    /**
     * @param mixed $idQuery
     * @return string
     */
    protected function get_ids($idQuery): string {
        if (is_array($idQuery)) {
            switch (count($idQuery)) {
                case 0:
                    return '0';
                case 1:
                    return sprintf('id=%d', reset($idQuery));
                default:
                    return sprintf('id IN (%s)', implode(',', $idQuery));
            }
        }
        if (is_int($idQuery)) {
            return sprintf('id=%d', $idQuery);
        }
        if (is_bool($idQuery)) {
            return '1';
        }
        return sprintf('id="%s"', $this->escape($idQuery));
    }
    
    /**
     * @param array $arr
     * @return string
     */
    protected function _get_update_data(array $arr): string {
        $ret = [];
        foreach ($arr as $key => $val) {
            if ($val === null) {
                $ret[] = sprintf('`%s`=NULL', $key);
            } elseif (is_int($val)) {
                $ret[] = sprintf('`%s`=%d', $key, $val);
            } else {
                $ret[] = sprintf('`%s`="%s"', $key, $this->escape($val));
            }
        }
        return implode(',', $ret);
    }
    
    /**
     * @param ?string $dbName
     * @param ?string $tableName
     * @return string
     */
    protected function get_handle(?string $dbName, ?string $tableName = null): string {
        if ($dbName === null) {
            $dbName = $tableName;
            $tableName = null;
        }
        return $tableName === null ? sprintf('`%s`', $dbName) : sprintf('`%s`.`%s`', $dbName, $tableName);
    }
    
    /**
     * @param ?string $sql
     * @return void
     */
    protected function error(?string $sql = null): void {
        $err = '';
        if ($sql) {
            $err .= 'ERROR querying statement: ';
            $err .= $sql . PHP_EOL;
        } else {
            $err .= 'ERROR while mysqling!';
            if (isset($this->sqli) and $this->sqli->error) {
                $err .= sprintf(' [%s] %s', $this->sqli->errno, $this->sqli->error);
            }
            $err .= PHP_EOL;
        }
        Manager::_createLog($err);
    }
}
