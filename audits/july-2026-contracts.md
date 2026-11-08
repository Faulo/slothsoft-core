# July 2026 contract audit — issue #4

Target: [Faulo/slothsoft-core#4](https://github.com/Faulo/slothsoft-core/issues/4). Base checkout: `4e474dec9fd0e22adcc86daae4ce31efa86f011d`. Review date: 2026-11-08.

## Method and disposition

Compared the primary commit with its parent and current implementations, native declarations, inherited interfaces, callers and failure paths. All primary additions are PHPDoc. The inventory uses **zero-context Git hunks** to identify every original documentation insertion, including partial additions to pre-existing comments. Rows record the complete parameter/return/throws tags of the affected method.

- **Retained**: original tags remain suitable for the body/interface contract.
- **Later revised**: already changed in the base checkout, including harmless nullable spelling or generic enrichment; not necessarily an original defect.
- **Corrected now**: this patch repairs/refines the affected contract.
- **Removed later**: method was subsequently removed; do not restore it.

Constructors have no return annotation. Exception-only PSR methods may document interface result types and exceptions. Real setters/lifecycle hooks remain void. Mixed remains intentional for JSON, configuration, arbitrary callback/array contents and dynamic COM results. GD resource/object differences are described explicitly. No suppressions, baseline additions or blanket void replacements were added.

## Surrounding commits and compatibility decisions

| Commit | Review and result |
| --- | --- |
| `7316cf2` | Preserved retrieveJSON=mixed and corrected PSR returns. Typed parameter additions conflicted with then-permitted psr/http-message 1.0; current ^1.1 already resolves this. No dependency downgrade/history rewrite. AbstractFilteredStream::__toString lost the trait's rewind behavior: restored with partial-read/repeated-cast regression. WaitingStream is nonseekable, so its override lost no rewind. ?array metadata wrappers return only []/null; delegating metadata remains mixed. |
| `239cc3f` | Reviewed constructor return removals across 62 files; preserve these corrections and their parameter annotations. |
| `02390c6` | Reviewed DBMS native properties, parameters and returned values. Manager singleton initialization was subsequently guarded with isset in ed87d9e. Scalar escape input narrowing is repaired in Client and both wrappers. Preserve boolean tableMove normalization, SQL echo removal and nullable results. |
| `ed87d9e` | Reviewed all 32 files, native signatures, nullable spelling, execute null-to-false and query parameter renames. Preserve published boolean failure behavior. Database::getTableList was later made natively nullable, but PHPDoc still needed null. Restore fraction slash corrupted into ???. Preserve/document later count/ID overflow exceptions. |
| `055b363` | run_shutdown:void matches discarded callback output. Image edit was in later-removed disabled thumbnail code. |
| `149c6d0` | Combined catches preserve handling; removing retrieveXML false assignment agrees with nullable DOMNode. Preserve subsequent DatabaseException retirement. |
| `ddca7be` and later | Preserve _solveChallenge nullable XMLHttpRequest correction, explicit failed-read/serialization/DOM guards, mysqli reporting restoration and deferred directory configuration. |

Native API changes in this patch only widen the three escape inputs to restore string conversion. No PHP 8-only native mixed/union signatures were introduced. Resources stay PHPDoc types. Full supported-PHP/dependency/OS matrices remain in GitHub CI and Jenkins; no remote build, commit, push or release was authorized.

## Primary commit inventory

Coverage: **112 files, 625 zero-context hunks, 2479 added lines, 616 added return tags (203 void)**. Dispositions: Retained: 308, Corrected now: 92, Later revised: 224, Removed later: 1.

### `src/CLI.php`

Setters discard configuration results; getters return configured values. Preserve flexible stream configuration and document process exit-code failure.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+20,4` / `setTotalTimeout` | Retained | `@param float $value; @return void` | `@param float $value; @return void` |
| H2 `+28,3` / `getTotalTimeout` | Retained | `@return float` | `@return float` |
| H3 `+43,4` / `setIdleTimeout` | Retained | `@param float $value; @return void` | `@param float $value; @return void` |
| H4 `+51,3` / `getIdleTimeout` | Retained | `@return float` | `@return float` |
| H5 `+66,4` / `setStdOut` | Retained | `@param mixed $value; @return void` | `@param mixed $value; @return void` |
| H6 `+74,3` / `getStdOut` | Retained | `@return mixed` | `@return mixed` |
| H7 `+89,4` / `setStdErr` | Retained | `@param mixed $value; @return void` | `@param mixed $value; @return void` |
| H8 `+97,3` / `getStdErr` | Retained | `@return mixed` | `@return mixed` |
| H9 `+104,5` / `execute` | Corrected now | `@param string $command; @param string\|null $workingDirectory; @return int` | `@param string $command; @param ?string $workingDirectory; @return int; @throws \RuntimeException` |

### `src/CacheDirectoryStorage.php`

Checked expiry/missing-file branches and retrieval forms against EphemeralStorageInterface. JSON may be scalar/array/null; preserve mixed and later serialization guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,3` / `_DOMHelper` | Retained | `@return DOMHelper` | `@return DOMHelper` |
| H2 `+41,4` / `__construct` | Later revised | `@param string $name; @return void` | `@param string $name` |
| H3 `+59,5` / `exists` | Retained | `@param string $name; @param int $modifyTime; @return bool` | `@param string $name; @param int $modifyTime; @return bool` |
| H4 `+69,5` / `retrieve` | Later revised | `@param string $name; @param int $modifyTime; @return string\|null` | `@param string $name; @param int $modifyTime; @return ?string` |
| H5 `+87,6` / `retrieveXML` | Later revised | `@param string $name; @param int $modifyTime; @param DOMDocument\|null $targetDoc; @return DOMNode\|null` | `@param string $name; @param int $modifyTime; @param ?DOMDocument $targetDoc; @return ?DOMNode` |
| H6 `+102,5` / `retrieveDocument` | Later revised | `@param string $name; @param int $modifyTime; @return DOMDocument\|null` | `@param string $name; @param int $modifyTime; @return ?DOMDocument` |
| H7 `+127,5` / `retrieveJSON` | Retained | `@param string $name; @param int $modifyTime; @return mixed` | `@param string $name; @param int $modifyTime; @return mixed` |
| H8 `+144,4` / `delete` | Retained | `@param string $name; @return bool` | `@param string $name; @return bool` |
| H9 `+155,6` / `store` | Retained | `@param string $name; @param string $payload; @param int $modifyTime; @return bool` | `@param string $name; @param string $payload; @param int $modifyTime; @return bool` |
| H10 `+175,6` / `storeXML` | Retained | `@param string $name; @param DOMNode $dataNode; @param int $modifyTime; @return bool` | `@param string $name; @param DOMNode $dataNode; @param int $modifyTime; @return bool` |
| H11 `+186,6` / `storeDocument` | Retained | `@param string $name; @param DOMDocument $dataDoc; @param int $modifyTime; @return bool` | `@param string $name; @param DOMDocument $dataDoc; @param int $modifyTime; @return bool` |
| H12 `+207,6` / `storeJSON` | Retained | `@param string $name; @param mixed $dataObject; @param int $modifyTime; @return bool` | `@param string $name; @param mixed $dataObject; @param int $modifyTime; @return bool` |

### `src/CascadingDictionary.php`

ArrayAccess setters/unsetters are void, offsetGet returns values by reference. Preserve string and integer iterator keys, including PHP numeric-string key conversion.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,3` / `__construct` | Later revised | `@return void` | `(none)` |
| H2 `+28,4` / `offsetExists` | Retained | `@param mixed $offset; @return bool` | `@param mixed $offset; @return bool` |
| H3 `+42,4` / `offsetGet` | Retained | `@param mixed $offset; @return mixed` | `@param mixed $offset; @return mixed` |
| H4 `+55,5` / `offsetSet` | Retained | `@param mixed $offset; @param mixed $value; @return void` | `@param mixed $offset; @param mixed $value; @return void` |
| H5 `+70,4` / `offsetUnset` | Retained | `@param mixed $offset; @return void` | `@param mixed $offset; @return void` |
| H6 `+78,3` / `getIterator` | Corrected now | `@return Traversable` | `@return Traversable<int\|string, mixed>` |

### `src/CloudFlareScraper.php`

Preserve ddca7be nullable challenge correction; document propagated null and guard public result access on unsolved challenges. Convert the default cookie FileInfo to its path before passing it to the native string setter.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+25,5` / `__construct` | Later revised | `@param mixed $cookieFile; @param mixed $trySolving; @return void` | `@param mixed $cookieFile; @param mixed $trySolving` |
| H2 `+35,4` / `getFile` | Later revised | `@param mixed $uri; @return string\|null` | `@param mixed $uri; @return ?string` |
| H3 `+44,4` / `getXPath` | Later revised | `@param mixed $uri; @return DOMXPath\|null` | `@param mixed $uri; @return ?DOMXPath` |
| H4 `+53,4` / `_httpRequest` | Corrected now | `@param mixed $uri; @return mixed` | `@param mixed $uri; @return XMLHttpRequest\|null` |
| H5 `+80,6` / `_solveChallenge` | Later revised | `@param mixed $requestURI; @param DOMDocument $htmlDoc; @param mixed $html; @return void` | `@param mixed $requestURI; @param DOMDocument $htmlDoc; @param mixed $html; @return ?XMLHttpRequest` |
| H6 `+145,4` / `_isProtected` | Retained | `@param mixed $html; @return bool` | `@param mixed $html; @return bool` |
| H7 `+153,4` / `_loadXPath` | Retained | `@param DOMDocument $doc; @return DOMXPath` | `@param DOMDocument $doc; @return DOMXPath` |
| H8 `+161,3` / `__serialize` | Later revised | `@return array` | `@return array<mixed>` |
| H9 `+171,4` / `__unserialize` | Later revised | `@param array $data; @return void` | `@param array<mixed> $data; @return void` |

### `src/Configuration/ConfigurationField.php`

Flexible configuration values; getters return configured values or throw, setters validate/store without returning. Preserve deferred directory-validation correction.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+11,4` / `__construct` | Later revised | `@param mixed $defaultValue; @return void` | `@param mixed $defaultValue` |
| H2 `+21,4` / `getValue` | Retained | `@return mixed; @throws ConfigurationRequiredException` | `@return mixed; @throws ConfigurationRequiredException` |
| H3 `+41,4` / `setValue` | Retained | `@param mixed $newValue; @return void` | `@param mixed $newValue; @return void` |
| H4 `+49,3` / `hasValue` | Retained | `@return bool` | `@return bool` |

### `src/Configuration/DirectoryConfigurationField.php`

Flexible configuration values; getters return configured values or throw, setters validate/store without returning. Preserve deferred directory-validation correction.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+11,6` / `setValue` | Later revised | `@param mixed $newValue; @return void; @throws BadMethodCallException; @throws RuntimeException` | `@param mixed $newValue; @return void; @throws BadMethodCallException` |

### `src/Configuration/FileConfigurationField.php`

Flexible configuration values; getters return configured values or throw, setters validate/store without returning. Preserve deferred directory-validation correction.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,5` / `setValue` | Retained | `@param mixed $newValue; @return void; @throws BadMethodCallException` | `@param mixed $newValue; @return void; @throws BadMethodCallException` |

### `src/Configuration/StorageConfigurationField.php`

Flexible configuration values; getters return configured values or throw, setters validate/store without returning. Preserve deferred directory-validation correction.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+11,5` / `setValue` | Retained | `@param mixed $newValue; @return void; @throws BadMethodCallException` | `@param mixed $newValue; @return void; @throws BadMethodCallException` |

### `src/DBMS/Authority.php`

Compared Client/Database/Table/Manager delegation, query construction and disconnected results. Preserve execute=false and boolean tableMove behavior. Restore scalar/stringable escape inputs; correct nullable table-list documentation. Identifier strings and later reporting fixes remain.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+21,6` / `__construct` | Later revised | `@param string $server; @param string $user; @param string $password; @return void` | `@param string $server; @param string $user; @param string $password` |

### `src/DBMS/Client.php`

Compared Client/Database/Table/Manager delegation, query construction and disconnected results. Preserve execute=false and boolean tableMove behavior. Restore scalar/stringable escape inputs; correct nullable table-list documentation. Identifier strings and later reporting fixes remain.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+47,4` / `setDefaultAuthority` | Retained | `@param Authority $authority; @return void` | `@param Authority $authority; @return void` |
| H2 `+55,3` / `getDefaultAuthority` | Retained | `@return Authority` | `@return Authority` |
| H3 `+62,3` / `clearDefaultAuthority` | Retained | `@return void` | `@return void` |
| H4 `+93,3` / `__construct` | Later revised | `@return void` | `(none)` |
| H5 `+99,4` / `reconnect` | Later revised | `@return bool; @throws DatabaseException` | `@return bool` |
| H6 `+122,3` / `disconnect` | Retained | `@return void` | `@return void` |
| H7 `+132,4` / `connect` | Later revised | `@return bool; @throws DatabaseException` | `@return bool` |
| H8 `+140,5` / `setDatabase` | Later revised | `@param mixed $dbName; @return mixed; @throws DatabaseException` | `@param string $dbName; @return ?bool` |
| H9 `+155,6` / `tableExists` | Later revised | `@param mixed $dbName; @param mixed $tableName; @return bool\|null; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @return ?bool` |
| H10 `+170,8` / `tableMove` | Later revised | `@param mixed $oldDbName; @param mixed $oldTableName; @param mixed $newDbName; @param mixed $newTableName; @return mixed; @throws DatabaseException` | `@param string $oldDbName; @param string $oldTableName; @param string $newDbName; @param string $newTableName; @return bool` |
| H11 `+185,5` / `databaseExists` | Later revised | `@param mixed $dbName; @return bool\|null; @throws DatabaseException` | `@param string $dbName; @return ?bool` |
| H12 `+199,4` / `getDatabaseList` | Later revised | `@return mixed; @throws DatabaseException` | `@return ?array<mixed>` |
| H13 `+211,5` / `getTableList` | Later revised | `@param mixed $dbName; @return mixed; @throws DatabaseException` | `@param string $dbName; @return ?array<mixed>` |
| H14 `+224,5` / `createDatabase` | Later revised | `@param mixed $dbName; @return void; @throws DatabaseException` | `@param string $dbName; @return void` |
| H15 `+237,5` / `deleteDatabase` | Later revised | `@param mixed $dbName; @return void; @throws DatabaseException` | `@param string $dbName; @return void` |
| H16 `+250,9` / `createTable` | Later revised | `@param mixed $dbName; @param mixed $tableName; @param array $cols; @param array $keys; @param array $options; @return void; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @param array<mixed> $cols; @param array<mixed> $keys; @param array<mixed> $options; @return void` |
| H17 `+293,7` / `addIndex` | Later revised | `@param mixed $dbName; @param mixed $tableName; @param mixed $index; @return void; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @param array<mixed>\|string $index; @return void` |
| H18 `+318,9` / `select` | Later revised | `@param mixed $dbName; @param mixed $tableName; @param mixed $cols; @param mixed $sqlString; @param mixed $sqlSuffix; @return mixed; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @param array<mixed>\|string\|bool $columnQuery; @param array<mixed>\|string $sqlQuery; @param string $sqlSuffix; @return ?array<mixed>` |
| H19 `+408,8` / `insert` | Corrected now | `@param mixed $dbName; @param mixed $tableName; @param mixed $insertData; @param mixed $onDuplicateData; @return mixed; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @param array<mixed> $insertData; @param array<mixed> $onDuplicateData; @return ?int; @throws \OverflowException` |
| H20 `+450,8` / `update` | Corrected now | `@param mixed $dbName; @param mixed $tableName; @param mixed $arr; @param mixed $id; @return mixed; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @param array<mixed> $arr; @param mixed $idQuery; @return ?int; @throws \OverflowException` |
| H21 `+473,7` / `delete` | Corrected now | `@param mixed $dbName; @param mixed $tableName; @param mixed $id; @return mixed; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @param mixed $idQuery; @return ?int; @throws \OverflowException` |
| H22 `+495,5` / `execute` | Later revised | `@param mixed $sqlString; @return mixed; @throws DatabaseException` | `@param string $sqlString; @return mysqli_result\|bool` |
| H23 `+508,4` / `executeFile` | Later revised | `@param mixed $file; @return mixed` | `@param string $file; @return ?bool` |
| H24 `+519,6` / `getColumns` | Later revised | `@param mixed $dbName; @param mixed $tableName; @return array\|null; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @return ?array<mixed>` |
| H25 `+544,6` / `optimize` | Later revised | `@param mixed $dbName; @param mixed $tableName; @return bool; @throws DatabaseException` | `@param string $dbName; @param string $tableName; @return bool` |
| H26 `+574,6` / `resetCharset` | Later revised | `@param mixed $dbName; @param mixed $tableName; @return void; @throws DatabaseException` | `@param string $dbName; @param ?string $tableName; @return void` |
| H27 `+605,5` / `escape` | Corrected now | `@param mixed $string; @return mixed; @throws DatabaseException` | `@param mixed $string; @return string` |
| H28 `+618,5` / `get_ids` | Later revised | `@param mixed $id; @return string; @throws DatabaseException` | `@param mixed $idQuery; @return string` |
| H29 `+655,5` / `_get_update_data` | Later revised | `@param array $arr; @return string; @throws DatabaseException` | `@param array<mixed> $arr; @return string` |
| H30 `+681,5` / `get_handle` | Later revised | `@param mixed $dbName; @param mixed $tableName; @return string` | `@param ?string $dbName; @param ?string $tableName; @return string` |
| H31 `+694,5` / `error` | Later revised | `@param mixed $sql; @return void; @throws DatabaseException` | `@param ?string $sql; @return void` |

### `src/DBMS/Database.php`

Compared Client/Database/Table/Manager delegation, query construction and disconnected results. Preserve execute=false and boolean tableMove behavior. Restore scalar/stringable escape inputs; correct nullable table-list documentation. Identifier strings and later reporting fixes remain.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,5` / `__construct` | Later revised | `@param Client $client; @param mixed $dbName; @return void` | `@param Client $client; @param string $dbName` |
| H2 `+30,3` / `getName` | Later revised | `@return mixed` | `@return string` |
| H3 `+37,4` / `tableExists` | Later revised | `@param mixed $tableName; @return bool\|null` | `@param string $tableName; @return ?bool` |
| H4 `+45,6` / `tableMove` | Later revised | `@param mixed $oldTableName; @param mixed $newTableName; @param mixed $newDbName; @return mixed` | `@param string $oldTableName; @param string $newTableName; @param ?string $newDbName; @return bool` |
| H5 `+58,3` / `databaseExists` | Later revised | `@return bool\|null` | `@return ?bool` |
| H6 `+65,3` / `getTableList` | Corrected now | `@return mixed` | `@return ?array<mixed>` |
| H7 `+72,4` / `getTable` | Later revised | `@param mixed $tableName; @return mixed` | `@param string $tableName; @return Table` |
| H8 `+81,3` / `createDatabase` | Retained | `@return void` | `@return void` |
| H9 `+88,3` / `deleteDatabase` | Retained | `@return void` | `@return void` |
| H10 `+96,7` / `createTable` | Later revised | `@param mixed $tableName; @param array $cols; @param array $keys; @param array $options; @return void` | `@param string $tableName; @param array<mixed> $cols; @param array<mixed> $keys; @param array<mixed> $options; @return void` |
| H11 `+109,7` / `select` | Later revised | `@param mixed $tableName; @param mixed $cols; @param mixed $sqlString; @param mixed $sqlSuffix; @return mixed` | `@param string $tableName; @param array<mixed>\|string\|bool $columnQuery; @param array<mixed>\|string $sqlQuery; @param string $sqlSuffix; @return ?array<mixed>` |
| H12 `+121,6` / `insert` | Later revised | `@param mixed $tableName; @param mixed $insertData; @param mixed $onDuplicateData; @return mixed` | `@param string $tableName; @param array<mixed> $insertData; @param array<mixed> $onDuplicateData; @return ?int` |
| H13 `+132,6` / `update` | Later revised | `@param mixed $tableName; @param mixed $arr; @param mixed $id; @return mixed` | `@param string $tableName; @param array<mixed> $arr; @param mixed $idQuery; @return ?int` |
| H14 `+143,5` / `delete` | Later revised | `@param mixed $tableName; @param mixed $id; @return mixed` | `@param string $tableName; @param mixed $idQuery; @return ?int` |
| H15 `+153,4` / `getColumns` | Later revised | `@param mixed $tableName; @return array\|null` | `@param string $tableName; @return ?array<mixed>` |
| H16 `+161,4` / `optimize` | Later revised | `@param mixed $tableName; @return bool\|null` | `@param ?string $tableName; @return ?bool` |
| H17 `+177,4` / `escape` | Corrected now | `@param mixed $string; @return mixed` | `@param mixed $string; @return string` |
| H18 `+185,5` / `addIndex` | Later revised | `@param mixed $tableName; @param mixed $index; @return void` | `@param string $tableName; @param array<mixed>\|string $index; @return void` |
| H19 `+194,3` / `resetCharset` | Retained | `@return void` | `@return void` |

### `src/DBMS/Manager.php`

Compared Client/Database/Table/Manager delegation, query construction and disconnected results. Preserve execute=false and boolean tableMove behavior. Restore scalar/stringable escape inputs; correct nullable table-list documentation. Identifier strings and later reporting fixes remain.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+28,4` / `setLogEnabled` | Retained | `@param bool $value; @return void` | `@param bool $value; @return void` |
| H2 `+36,3` / `getLogEnabled` | Retained | `@return bool` | `@return bool` |
| H3 `+51,4` / `setLogDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H4 `+59,3` / `getLogDirectory` | Retained | `@return string` | `@return string` |
| H5 `+74,3` / `getClient` | Retained | `@return Client` | `@return Client` |
| H6 `+85,4` / `getDatabase` | Later revised | `@param mixed $dbName; @return mixed` | `@param string $dbName; @return Database` |
| H7 `+99,5` / `getTable` | Later revised | `@param mixed $dbName; @param mixed $tableName; @return mixed` | `@param string $dbName; @param string $tableName; @return Table` |
| H8 `+118,3` / `cron` | Retained | `@return void` | `@return void` |
| H9 `+151,4` / `_createLog` | Later revised | `@param mixed $sql; @return void` | `@param string $sql; @return void` |

### `src/DBMS/Table.php`

Compared Client/Database/Table/Manager delegation, query construction and disconnected results. Preserve execute=false and boolean tableMove behavior. Restore scalar/stringable escape inputs; correct nullable table-list documentation. Identifier strings and later reporting fixes remain.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,5` / `__construct` | Later revised | `@param Database $db; @param mixed $tableName; @return void` | `@param Database $db; @param string $tableName` |
| H2 `+29,3` / `getName` | Later revised | `@return mixed` | `@return string` |
| H3 `+36,3` / `tableExists` | Later revised | `@return bool\|null` | `@return ?bool` |
| H4 `+43,5` / `tableMove` | Later revised | `@param mixed $newTableName; @param mixed $newDbName; @return mixed` | `@param string $newTableName; @param ?string $newDbName; @return bool` |
| H5 `+53,6` / `createTable` | Later revised | `@param array $cols; @param array $keys; @param array $options; @return void` | `@param array<mixed> $cols; @param array<mixed> $keys; @param array<mixed> $options; @return void` |
| H6 `+65,6` / `select` | Later revised | `@param mixed $cols; @param mixed $sqlString; @param mixed $sqlSuffix; @return mixed` | `@param array<mixed>\|string\|bool $columnQuery; @param array<mixed>\|string $sqlQuery; @param string $sqlSuffix; @return ?array<mixed>` |
| H7 `+76,5` / `insert` | Later revised | `@param mixed $insertData; @param mixed $onDuplicateData; @return mixed` | `@param array<mixed> $insertData; @param array<mixed> $onDuplicateData; @return ?int` |
| H8 `+86,5` / `update` | Later revised | `@param mixed $arr; @param mixed $id; @return mixed` | `@param array<mixed> $arr; @param mixed $idQuery; @return ?int` |
| H9 `+96,4` / `delete` | Later revised | `@param mixed $id; @return mixed` | `@param mixed $idQuery; @return ?int` |
| H10 `+105,3` / `getColumns` | Later revised | `@return array\|null` | `@return ?array<mixed>` |
| H11 `+112,3` / `optimize` | Later revised | `@return bool\|null` | `@return ?bool` |
| H12 `+119,4` / `escape` | Corrected now | `@param mixed $string; @return mixed` | `@param mixed $string; @return string` |
| H13 `+127,4` / `addIndex` | Later revised | `@param mixed $index; @return void` | `@param array<mixed>\|string $index; @return void` |

### `src/DOMHelper.php`

Compared static loaders, parser, serializer, XSLT adapters and normalization. Preserve DOM/document/fragment results and later missing-element/read guards; document serializer exceptions.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+121,5` / `loadDocument` | Retained | `@param string $filePath; @param bool $asHTML; @return DOMDocument` | `@param string $filePath; @param bool $asHTML; @return DOMDocument` |
| H2 `+136,5` / `parseDocument` | Retained | `@param string $fileContents; @param bool $asHTML; @return DOMDocument` | `@param string $fileContents; @param bool $asHTML; @return DOMDocument` |
| H3 `+151,5` / `loadXPath` | Retained | `@param DOMDocument $document; @param int $options; @return DOMXPath` | `@param DOMDocument $document; @param int $options; @return DOMXPath` |
| H4 `+183,4` / `guessExtension` | Retained | `@param string $namespaceURI; @return string` | `@param string $namespaceURI; @return string` |
| H5 `+212,7` / `parse` | Later revised | `@param string $xmlCode; @param DOMDocument\|null $targetDoc; @param bool $asHTML; @return DOMDocumentFragment; @throws RuntimeException` | `@param string $xmlCode; @param ?DOMDocument $targetDoc; @param bool $asHTML; @return DOMDocumentFragment; @throws RuntimeException` |
| H6 `+278,6` / `createDocument` | Retained | `@param string $namespaceURI; @param string $qualifiedName; @return DOMDocument; @throws DOMException` | `@param string $namespaceURI; @param string $qualifiedName; @return DOMDocument; @throws DOMException` |
| H7 `+288,4` / `stringify` | Corrected now | `@param DOMNode $sourceNode; @return string` | `@param DOMNode $sourceNode; @return string; @throws RuntimeException` |
| H8 `+296,5` / `load` | Retained | `@param mixed $url; @param bool $asHTML; @return DOMDocument` | `@param mixed $url; @param bool $asHTML; @return DOMDocument` |
| H9 `+330,6` / `transformToDocument` | Later revised | `@param mixed $source; @param mixed $template; @param array $param; @return DOMDocument` | `@param mixed $source; @param mixed $template; @param array<mixed> $param; @return DOMDocument` |
| H10 `+340,7` / `transformToFile` | Later revised | `@param mixed $source; @param mixed $template; @param array $param; @param SplFileInfo\|null $output; @return SplFileInfo` | `@param mixed $source; @param mixed $template; @param array<mixed> $param; @param ?SplFileInfo $output; @return SplFileInfo` |
| H11 `+357,7` / `transformToFragment` | Later revised | `@param mixed $source; @param mixed $template; @param array $param; @param DOMDocument\|null $targetDoc; @return DOMDocumentFragment` | `@param mixed $source; @param mixed $template; @param array<mixed> $param; @param ?DOMDocument $targetDoc; @return DOMDocumentFragment` |
| H12 `+390,2` / `normalizeDocument` | Retained | `@param DOMDocument $dataDoc; @return DOMDocument` | `@param DOMDocument $dataDoc; @return DOMDocument` |

### `src/EphemeralStorageInterface.php`

retrieveJSON was already corrected to mixed by 7316cf2. Nullable XML/document/string paths and boolean persistence methods remain.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,5` / `exists` | Retained | `@param string $name; @param int $modifyTime; @return bool` | `@param string $name; @param int $modifyTime; @return bool` |
| H2 `+24,5` / `retrieve` | Later revised | `@param string $name; @param int $modifyTime; @return string\|null` | `@param string $name; @param int $modifyTime; @return ?string` |
| H3 `+31,6` / `retrieveXML` | Later revised | `@param string $name; @param int $modifyTime; @param DOMDocument\|null $targetDoc; @return DOMNode\|null` | `@param string $name; @param int $modifyTime; @param ?DOMDocument $targetDoc; @return ?DOMNode` |
| H4 `+39,5` / `retrieveDocument` | Later revised | `@param string $name; @param int $modifyTime; @return DOMDocument\|null` | `@param string $name; @param int $modifyTime; @return ?DOMDocument` |
| H5 `+46,5` / `retrieveJSON` | Later revised | `@param string $name; @param int $modifyTime; @return void` | `@param string $name; @param int $modifyTime; @return mixed` |
| H6 `+53,4` / `delete` | Retained | `@param string $name; @return bool` | `@param string $name; @return bool` |
| H7 `+59,6` / `store` | Retained | `@param string $name; @param string $payload; @param int $modifyTime; @return bool` | `@param string $name; @param string $payload; @param int $modifyTime; @return bool` |
| H8 `+67,6` / `storeXML` | Retained | `@param string $name; @param DOMNode $dataNode; @param int $modifyTime; @return bool` | `@param string $name; @param DOMNode $dataNode; @param int $modifyTime; @return bool` |
| H9 `+75,6` / `storeDocument` | Retained | `@param string $name; @param DOMDocument $dataDoc; @param int $modifyTime; @return bool` | `@param string $name; @param DOMDocument $dataDoc; @param int $modifyTime; @return bool` |
| H10 `+83,6` / `storeJSON` | Retained | `@param string $name; @param mixed $dataObject; @param int $modifyTime; @return bool` | `@param string $name; @param mixed $dataObject; @param int $modifyTime; @return bool` |

### `src/FileSystem.php`

Checked COM fallback, stat failure/null, scan modes, archive/URI/CSV paths. Document dynamic COM results, entry union, timestamp map/false and CSV nested lists/null. Restore fraction slash corrupted by ed87d9e and guard failed timestamp maps before max().

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+93,4` / `ensureDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H2 `+107,4` / `isVideo` | Retained | `@param string $fileName; @return bool` | `@param string $fileName; @return bool` |
| H3 `+115,4` / `isAudio` | Retained | `@param string $fileName; @return bool` | `@param string $fileName; @return bool` |
| H4 `+123,4` / `isSubttitle` | Retained | `@param string $fileName; @return bool` | `@param string $fileName; @return bool` |
| H5 `+131,5` / `drawBytes` | Retained | `@param mixed $size; @param int $precision; @return string` | `@param mixed $size; @param int $precision; @return string` |
| H6 `+151,4` / `setStorage` | Retained | `@param EphemeralStorageInterface $storage; @return void` | `@param EphemeralStorageInterface $storage; @return void` |
| H7 `+159,3` / `getStorage` | Retained | `@return EphemeralStorageInterface` | `@return EphemeralStorageInterface` |
| H8 `+166,6` / `generateStorageKey` | Retained | `@param string $path; @param string $hash; @return string; @throws Exception` | `@param string $path; @param string $hash; @return string; @throws Exception` |
| H9 `+183,7` / `asNode` | Later revised | `@param string $path; @param DOMDocument\|null $dataDoc; @return DOMNode\|null; @throws DOMException; @throws Exception` | `@param string $path; @param ?DOMDocument $dataDoc; @return ?DOMNode; @throws DOMException; @throws Exception` |
| H10 `+304,5` / `webpath` | Retained | `@param string $fileName; @param bool $absolute; @return string` | `@param string $fileName; @param bool $absolute; @return string` |
| H11 `+313,4` / `size` | Later revised | `@param string $fileName; @return int\|null` | `@param string $fileName; @return ?int` |
| H12 `+327,4` / `free` | Later revised | `@param string $fileName; @return float\|null` | `@param string $fileName; @return ?float` |
| H13 `+336,4` / `mime` | Later revised | `@param string $fileName; @return string\|null` | `@param string $fileName; @return ?string` |
| H14 `+346,4` / `extension` | Retained | `@param string $fileName; @return string` | `@param string $fileName; @return string` |
| H15 `+354,4` / `changetime` | Later revised | `@param string $fileName; @return int\|null` | `@param string $fileName; @return ?int` |
| H16 `+369,4` / `maketime` | Later revised | `@param string $fileName; @return int\|null` | `@param string $fileName; @return ?int` |
| H17 `+384,4` / `lookupFile` | Corrected now | `@param string $fileName; @return mixed` | `@param string $fileName; @return mixed A COM filesystem automation object, or null when unavailable.` |
| H18 `+408,5` / `filenameEncode` | Retained | `@param string $filename; @param bool $removeRoot; @return string` | `@param string $filename; @param bool $removeRoot; @return string` |
| H19 `+427,4` / `filenameSanitize` | Retained | `@param string $filename; @return string` | `@param string $filename; @return string` |
| H20 `+460,5` / `download` | Retained | `@param mixed $filePath; @param mixed $downloadName; @return void` | `@param mixed $filePath; @param mixed $downloadName; @return void` |
| H21 `+505,6` / `outputChunk` | Retained | `@param mixed $filePath; @param mixed $start; @param mixed $length; @return void` | `@param mixed $filePath; @param mixed $start; @param mixed $length; @return void` |
| H22 `+639,5` / `dirModifyTime` | Corrected now | `@param string $dirPath; @param string\|null $rootPath; @return mixed` | `@param string $dirPath; @param ?string $rootPath; @return non-empty-array<int\|string, int\|null>\|false` |
| H23 `+668,5` / `mediaInfo` | Later revised | `@param string $filePath; @return array; @throws Exception` | `@param string $filePath; @return array<mixed>; @throws Exception` |
| H24 `+727,6` / `extractArchive` | Corrected now | `@param mixed $archivePath; @param mixed $targetDirectory; @return mixed; @throws Exception` | `@param mixed $archivePath; @param mixed $targetDirectory; @return bool; @throws Exception` |
| H25 `+746,7` / `archivePath` | Corrected now | `@param mixed $directoryPath; @param mixed $archiveName; @param mixed $checkUpdate; @return mixed; @throws Exception` | `@param mixed $directoryPath; @param mixed $archiveName; @param mixed $checkUpdate; @return bool; @throws Exception` |
| H26 `+793,7` / `downloadByURI` | Corrected now | `@param mixed $destPath; @param mixed $sourceURI; @param array $options; @return mixed; @throws Exception` | `@param mixed $destPath; @param mixed $sourceURI; @param array<mixed> $options; @return string; @throws Exception` |
| H27 `+840,5` / `getLinkByXPath` | Corrected now | `@param mixed $sourceURI; @param mixed $linkQuery; @return mixed` | `@param mixed $sourceURI; @param mixed $linkQuery; @return string\|false` |
| H28 `+864,7` / `loadCSV` | Corrected now | `@param string $path; @param string $delimiter; @param string $enclosure; @param string $escape; @return array\|null` | `@param string $path; @param string $delimiter; @param string $enclosure; @param string $escape; @return list<list<string\|null>\|null>\|null` |
| H29 `+924` / `removeDir` | Retained | `@param string $path; @param bool $keepRoot; @return void` | `@param string $path; @param bool $keepRoot; @return void` |
| H30 `+960,3` / `copy` | Retained | `@param string $from; @param string $to; @return void; @throws InvalidArgumentException; @throws DirectoryCouldNotBeCreatedException` | `@param string $from; @param string $to; @return void; @throws InvalidArgumentException; @throws DirectoryCouldNotBeCreatedException` |

### `src/Game/Dice.php`

Compared numerical/string generation bodies. Preserve flexible dice inputs and arithmetic; prime list values are integers. No native input narrowing.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,5` / `__construct` | Later revised | `@param mixed $sidesCount; @param mixed $diceCount; @return void` | `@param mixed $sidesCount; @param mixed $diceCount` |
| H2 `+29,3` / `getMax` | Retained | `@return mixed` | `@return mixed` |
| H3 `+36,3` / `getMin` | Retained | `@return mixed` | `@return mixed` |
| H4 `+43,3` / `getAverage` | Retained | `@return mixed` | `@return mixed` |
| H5 `+54,4` / `enclose` | Retained | `@param mixed $value; @return mixed` | `@param mixed $value; @return mixed` |
| H6 `+62,3` / `roll` | Retained | `@return int` | `@return int` |
| H7 `+73,3` / `rollImpossible` | Retained | `@return bool` | `@return bool` |
| H8 `+82,4` / `rollHigher` | Retained | `@param mixed $value; @return bool` | `@param mixed $value; @return bool` |
| H9 `+92,4` / `rollLower` | Retained | `@param mixed $value; @return bool` | `@param mixed $value; @return bool` |
| H10 `+100,4` / `rollEqual` | Retained | `@param mixed $value; @return bool` | `@param mixed $value; @return bool` |
| H11 `+108,5` / `rand` | Retained | `@param mixed $min; @param mixed $max; @return int` | `@param mixed $min; @param mixed $max; @return int` |

### `src/Game/Name.php`

Compared numerical/string generation bodies. Preserve flexible dice inputs and arithmetic; prime list values are integers. No native input narrowing.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+34,4` / `generate` | Later revised | `@param array $config; @return array` | `@param array<mixed> $config; @return array<mixed>` |

### `src/Game/Prime.php`

Compared numerical/string generation bodies. Preserve flexible dice inputs and arithmetic; prime list values are integers. No native input narrowing.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `getPrimeList` | Corrected now | `@param mixed $size; @return array` | `@param mixed $size; @return list<int>` |
| H2 `+33,4` / `isPrime` | Retained | `@param mixed $num; @return bool` | `@param mixed $num; @return bool` |

### `src/IO/FileInfo.php`

toFile returns the receiver as SplFileInfo; toString returns bytes or throws on failed reads.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+12,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |
| H2 `+19,3` / `toString` | Corrected now | `@return string` | `@return string; @throws \RuntimeException` |

### `src/IO/FileInfoFactory.php`

Factories return FileInfo; createFromResource also accepts string/array forms supported by file_put_contents. Document resource input while preserving all file_put_contents data forms without narrowing to resources.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+12,3` / `createTempFile` | Retained | `@return FileInfo` | `@return FileInfo` |
| H2 `+19,4` / `createFromPath` | Retained | `@param string $path; @return FileInfo` | `@param string $path; @return FileInfo` |
| H3 `+27,4` / `createDirectoryFromPath` | Retained | `@param string $path; @return FileInfo` | `@param string $path; @return FileInfo` |
| H4 `+36,4` / `createFromString` | Retained | `@param string $data; @return FileInfo` | `@param string $data; @return FileInfo` |
| H5 `+46,4` / `createFromDocument` | Retained | `@param DOMDocument $document; @return FileInfo` | `@param DOMDocument $document; @return FileInfo` |
| H6 `+56,4` / `createFromResource` | Corrected now | `@param mixed $resource; @return FileInfo` | `@param mixed $resource A stream resource or any data accepted by file_put_contents().; @return FileInfo` |
| H7 `+66,5` / `createFromUpload` | Retained | `@param string $tmpName; @return FileInfo; @throws InvalidArgumentException` | `@param string $tmpName; @return FileInfo; @throws InvalidArgumentException` |

### `src/IO/HTTPFile.php`

Checked factories, nullable downloads and file writes. Document int|false write results; preserve resource input and flexible copy callbacks.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+49,4` / `createFromTemp` | Retained | `@param string $fileName; @return HTTPFile` | `@param string $fileName; @return HTTPFile` |
| H2 `+92` / `createFromFileList` | Later revised | `@param array $content; @param string $fileName; @return HTTPFile; @param array $fileList` | `@param string $fileName; @param array<mixed> $fileList; @return HTTPFile` |
| H3 `+256,3` / `getPath` | Retained | `@return string` | `@return string` |
| H4 `+263,3` / `getName` | Retained | `@return string` | `@return string` |
| H5 `+270,3` / `getContents` | Corrected now | `@return string` | `@return string; @throws \RuntimeException` |
| H6 `+277,4` / `setContents` | Corrected now | `@param string $content; @return mixed` | `@param string $content; @return int\|false` |
| H7 `+285,4` / `setStream` | Corrected now | `@param mixed $content; @return mixed` | `@param mixed $content; @return int\|false` |
| H8 `+293,3` / `getDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |
| H9 `+300,4` / `setDocument` | Corrected now | `@param DOMDocument $content; @return mixed` | `@param DOMDocument $content; @return int\|false` |
| H10 `+308,6` / `copyTo` | Retained | `@param mixed $dir; @param mixed $name; @param mixed $copyClosure; @return mixed` | `@param mixed $dir; @param mixed $name; @param mixed $copyClosure; @return mixed` |
| H11 `+337,3` / `delete` | Retained | `@return bool` | `@return bool` |
| H12 `+344,3` / `exists` | Retained | `@return bool` | `@return bool` |
| H13 `+351,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |
| H14 `+358,3` / `toString` | Retained | `@return string` | `@return string` |
| H15 `+365,3` / `toFileName` | Retained | `@return string` | `@return string` |

### `src/IO/HTTPStream.php`

Transitions and parsing hooks are void; getters expose nullable strings, string header map, integer state and float timing values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+51,3` / `getMime` | Corrected now | `@return mixed` | `@return string\|null` |
| H2 `+58,3` / `getEncoding` | Corrected now | `@return mixed` | `@return string\|null` |
| H3 `+65,3` / `getHeaderList` | Corrected now | `@return mixed` | `@return array<string, string>` |
| H4 `+72,3` / `setStatusError` | Retained | `@return void` | `@return void` |
| H5 `+79,3` / `setStatusDone` | Retained | `@return void` | `@return void` |
| H6 `+86,3` / `setStatusRetry` | Retained | `@return void` | `@return void` |
| H7 `+93,3` / `setStatusContent` | Retained | `@return void` | `@return void` |
| H8 `+100,3` / `setStatusContentDone` | Retained | `@return void` | `@return void` |
| H9 `+107,3` / `getStatus` | Corrected now | `@return mixed` | `@return int` |
| H10 `+115,3` / `parseStatus` | Retained | `@return void` | `@return void` |
| H11 `+120,3` / `getContent` | Corrected now | `@return mixed` | `@return string` |
| H12 `+128,3` / `parseContent` | Retained | `@return void` | `@return void` |
| H13 `+133,3` / `getSleepDuration` | Corrected now | `@return mixed` | `@return float` |
| H14 `+140,3` / `getHeartbeatInterval` | Corrected now | `@return mixed` | `@return float` |
| H15 `+147,3` / `getHeartbeatTimeout` | Corrected now | `@return mixed` | `@return float` |
| H16 `+154,3` / `getHeartbeatContent` | Corrected now | `@return mixed` | `@return string\|null` |
| H17 `+161,3` / `getHeartbeatEOL` | Corrected now | `@return mixed` | `@return string\|null` |
| H18 `+168,3` / `__toString` | Corrected now | `@return mixed` | `@return string` |

### `src/IO/Psr7/AbstractFilteredStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+28,4` / `__construct` | Later revised | `@param StreamInterface $stream; @return void` | `@param StreamInterface $stream` |
| H2 `+37,5` / `read` | Later revised | `@param mixed $length; @return mixed; @throws RuntimeException` | `@param int $length; @return string; @throws RuntimeException` |
| H3 `+63,3` / `getContents` | Later revised | `@return mixed` | `@return string` |
| H4 `+74,3` / `eof` | Later revised | `@return mixed` | `@return bool` |
| H5 `+81,3` / `isSeekable` | Later revised | `@return mixed` | `@return bool` |
| H6 `+88,3` / `getSize` | Later revised | `@return mixed` | `@return ?int` |
| H7 `+101,6` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return void; @throws BadMethodCallException` | `@param int $offset; @param int $whence; @return void; @throws BadMethodCallException` |
| H8 `+116,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H9 `+121,4` / `processPayload` | Retained | `@param string $data; @return string` | `@param string $data; @return string` |
| H10 `+127,3` / `processFooter` | Retained | `@return string` | `@return string` |

### `src/IO/Psr7/AbstractFilteredStreamTestCase.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+14,3` / `getInput` | Retained | `@return string` | `@return string` |
| H2 `+19,4` / `calculateExpectedResult` | Retained | `@param string $input; @return string` | `@param string $input; @return string` |
| H3 `+25,3` / `getFilterFactory` | Retained | `@return FilteredStreamWriterInterface` | `@return FilteredStreamWriterInterface` |
| H4 `+36,3` / `setUp` | Retained | `@return void` | `@return void` |
| H5 `+46,3` / `testReadStream` | Retained | `@return void` | `@return void` |

### `src/IO/Psr7/ChunkedFilteredStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H2 `+15,4` / `processPayload` | Retained | `@param string $data; @return string` | `@param string $data; @return string` |
| H3 `+23,3` / `processFooter` | Retained | `@return string` | `@return string` |

### `src/IO/Psr7/ChunkedFilteredStreamFactory.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+11,4` / `toFilteredStream` | Retained | `@param StreamInterface $stream; @return StreamInterface` | `@param StreamInterface $stream; @return StreamInterface` |

### `src/IO/Psr7/LazyFileWriterStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,4` / `__construct` | Later revised | `@param FileWriterInterface $writer; @return void` | `@param FileWriterInterface $writer` |
| H2 `+45,3` / `__toString` | Later revised | `@return mixed` | `@return string` |
| H3 `+53,3` / `close` | Retained | `@return void` | `@return void` |
| H4 `+61,3` / `detach` | Later revised | `@return mixed` | `@return null` |
| H5 `+69,4` / `getMetadata` | Later revised | `@param mixed $key; @return array\|null` | `@param ?string $key; @return ?array<mixed>` |
| H6 `+77,3` / `getContents` | Later revised | `@return mixed` | `@return string` |
| H7 `+86,3` / `getSize` | Later revised | `@return mixed` | `@return ?int` |
| H8 `+93,3` / `tell` | Corrected now | `@return mixed` | `@return int; @throws \RuntimeException` |
| H9 `+100,3` / `eof` | Retained | `@return bool` | `@return bool` |
| H10 `+107,3` / `isSeekable` | Retained | `@return bool` | `@return bool` |
| H11 `+114,5` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return mixed` | `@param int $offset; @param int $whence; @return void` |
| H12 `+127,3` / `rewind` | Retained | `@return void` | `@return void` |
| H13 `+134,3` / `isWritable` | Retained | `@return bool` | `@return bool` |
| H14 `+141,5` / `write` | Later revised | `@param mixed $string; @return void; @throws BadMethodCallException` | `@param string $string; @return int; @throws BadMethodCallException` |
| H15 `+150,3` / `isReadable` | Retained | `@return bool` | `@return bool` |
| H16 `+157,4` / `read` | Corrected now | `@param mixed $length; @return mixed` | `@param int $length; @return string; @throws \RuntimeException` |

### `src/IO/Psr7/LazyStringWriterStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+29,4` / `__construct` | Later revised | `@param StringWriterInterface $writer; @return void` | `@param StringWriterInterface $writer` |
| H2 `+50,3` / `__toString` | Later revised | `@return mixed` | `@return string` |
| H3 `+58,3` / `close` | Retained | `@return void` | `@return void` |
| H4 `+69,3` / `detach` | Later revised | `@return mixed` | `@return null` |
| H5 `+77,4` / `getMetadata` | Later revised | `@param mixed $key; @return array\|null` | `@param ?string $key; @return ?array<mixed>` |
| H6 `+85,3` / `getContents` | Later revised | `@return mixed` | `@return string` |
| H7 `+100,3` / `getSize` | Retained | `@return int` | `@return int` |
| H8 `+108,3` / `tell` | Retained | `@return int` | `@return int` |
| H9 `+115,3` / `eof` | Retained | `@return bool` | `@return bool` |
| H10 `+123,3` / `isSeekable` | Retained | `@return bool` | `@return bool` |
| H11 `+130,7` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return void; @throws BadMethodCallException; @throws OutOfBoundsException` | `@param int $offset; @param int $whence; @return void; @throws BadMethodCallException; @throws OutOfBoundsException` |
| H12 `+161,3` / `rewind` | Retained | `@return void` | `@return void` |
| H13 `+168,3` / `isWritable` | Retained | `@return bool` | `@return bool` |
| H14 `+175,5` / `write` | Later revised | `@param mixed $string; @return void; @throws BadMethodCallException` | `@param string $string; @return int; @throws BadMethodCallException` |
| H15 `+184,3` / `isReadable` | Retained | `@return bool` | `@return bool` |
| H16 `+191,4` / `read` | Later revised | `@param mixed $length; @return mixed` | `@param int $length; @return string` |

### `src/IO/Psr7/OneTimeGeneratorStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+27,4` / `__construct` | Later revised | `@param ChunkWriterInterface $writer; @return void` | `@param ChunkWriterInterface $writer` |
| H2 `+44,3` / `eof` | Retained | `@return bool` | `@return bool` |
| H3 `+53,3` / `rewind` | Retained | `@return void` | `@return void` |
| H4 `+60,3` / `close` | Retained | `@return void` | `@return void` |
| H5 `+69,3` / `detach` | Later revised | `@return void` | `@return null` |
| H6 `+76,4` / `getMetadata` | Later revised | `@param mixed $key; @return array\|null` | `@param ?string $key; @return ?array<mixed>` |
| H7 `+84,4` / `getContents` | Retained | `@return string; @throws BadMethodCallException` | `@return string; @throws BadMethodCallException` |
| H8 `+92,3` / `__toString` | Later revised | `@return mixed` | `@return string` |
| H9 `+99,3` / `getSize` | Later revised | `@return mixed` | `@return null` |
| H10 `+106,4` / `tell` | Later revised | `@return void; @throws BadMethodCallException` | `@return int; @throws BadMethodCallException` |
| H11 `+114,3` / `isReadable` | Retained | `@return bool` | `@return bool` |
| H12 `+127,5` / `read` | Later revised | `@param mixed $length; @return mixed; @throws BadMethodCallException` | `@param int $length; @return string; @throws BadMethodCallException` |
| H13 `+164,3` / `isSeekable` | Retained | `@return bool` | `@return bool` |
| H14 `+171,6` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return void; @throws BadMethodCallException` | `@param int $offset; @param int $whence; @return void; @throws BadMethodCallException` |
| H15 `+181,3` / `isWritable` | Retained | `@return bool` | `@return bool` |
| H16 `+188,5` / `write` | Later revised | `@param mixed $string; @return void; @throws BadMethodCallException` | `@param string $string; @return int; @throws BadMethodCallException` |

### `src/IO/Psr7/PersistentGeneratorStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+29,4` / `__construct` | Later revised | `@param ChunkWriterInterface $writer; @return void` | `@param ChunkWriterInterface $writer` |
| H2 `+50,3` / `eof` | Retained | `@return bool` | `@return bool` |
| H3 `+59,3` / `rewind` | Retained | `@return void` | `@return void` |
| H4 `+66,3` / `close` | Retained | `@return void` | `@return void` |
| H5 `+77,3` / `detach` | Later revised | `@return mixed` | `@return null` |
| H6 `+85,4` / `getMetadata` | Later revised | `@param mixed $key; @return array\|null` | `@param ?string $key; @return ?array<mixed>` |
| H7 `+93,3` / `getContents` | Later revised | `@return mixed` | `@return string` |
| H8 `+108,3` / `__toString` | Later revised | `@return mixed` | `@return string` |
| H9 `+123,3` / `getSize` | Retained | `@return int` | `@return int` |
| H10 `+138,3` / `tell` | Retained | `@return int` | `@return int` |
| H11 `+145,3` / `isReadable` | Retained | `@return bool` | `@return bool` |
| H12 `+158,4` / `read` | Later revised | `@param mixed $length; @return mixed` | `@param int $length; @return string` |
| H13 `+194,3` / `isSeekable` | Retained | `@return bool` | `@return bool` |
| H14 `+201,6` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return void; @throws BadMethodCallException` | `@param int $offset; @param int $whence; @return void; @throws BadMethodCallException` |
| H15 `+219,3` / `isWritable` | Retained | `@return bool` | `@return bool` |
| H16 `+226,5` / `write` | Later revised | `@param mixed $string; @return void; @throws BadMethodCallException` | `@param string $string; @return int; @throws BadMethodCallException` |

### `src/IO/Psr7/ProcessStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,4` / `__construct` | Later revised | `@param string $command; @return void` | `@param string $command` |
| H2 `+39,3` / `eof` | Retained | `@return bool` | `@return bool` |
| H3 `+48,3` / `rewind` | Retained | `@return void` | `@return void` |
| H4 `+55,3` / `close` | Retained | `@return void` | `@return void` |
| H5 `+65,3` / `detach` | Later revised | `@return mixed` | `@return ?resource` |
| H6 `+74,4` / `getMetadata` | Later revised | `@param mixed $key; @return array\|null` | `@param ?string $key; @return ?array<mixed>` |
| H7 `+82,3` / `getContents` | Retained | `@return string` | `@return string` |
| H8 `+93,3` / `__toString` | Later revised | `@return mixed` | `@return string` |
| H9 `+100,3` / `getSize` | Later revised | `@return mixed` | `@return null` |
| H10 `+107,4` / `tell` | Later revised | `@return void; @throws BadMethodCallException` | `@return int; @throws BadMethodCallException` |
| H11 `+115,3` / `isReadable` | Retained | `@return bool` | `@return bool` |
| H12 `+122,4` / `read` | Corrected now | `@param mixed $length; @return mixed` | `@param int $length; @return string; @throws RuntimeException` |
| H13 `+136,3` / `isSeekable` | Retained | `@return bool` | `@return bool` |
| H14 `+143,6` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return void; @throws BadMethodCallException` | `@param int $offset; @param int $whence; @return void; @throws BadMethodCallException` |
| H15 `+157,3` / `isWritable` | Retained | `@return bool` | `@return bool` |
| H16 `+164,5` / `write` | Later revised | `@param mixed $string; @return void; @throws BadMethodCallException` | `@param string $string; @return int; @throws BadMethodCallException` |

### `src/IO/Psr7/StreamHelper.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,5` / `cacheStream` | Retained | `@param StreamInterface $input; @param mixed $chunkSize; @return StreamInterface` | `@param StreamInterface $input; @param mixed $chunkSize; @return StreamInterface` |
| H2 `+27,6` / `sliceStream` | Retained | `@param StreamInterface $input; @param int $offset; @param int $length; @return StreamInterface` | `@param StreamInterface $input; @param int $offset; @param int $length; @return StreamInterface` |

### `src/IO/Psr7/WaitingStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,6` / `__construct` | Later revised | `@param StreamInterface $stream; @param int $waitInMicroseconds; @param array\|null $heartbeat; @return void` | `@param StreamInterface $stream; @param int $waitInMicroseconds; @param ?array{interval: int, content: string} $heartbeat` |
| H2 `+31,3` / `isReadable` | Retained | `@return bool` | `@return bool` |
| H3 `+38,4` / `read` | Later revised | `@param mixed $length; @return mixed` | `@param int $length; @return string` |
| H4 `+61,3` / `isSeekable` | Retained | `@return bool` | `@return bool` |
| H5 `+68,6` / `seek` | Later revised | `@param mixed $offset; @param mixed $whence; @return void; @throws BadMethodCallException` | `@param int $offset; @param int $whence; @return void; @throws BadMethodCallException` |
| H6 `+78,3` / `isWritable` | Retained | `@return bool` | `@return bool` |
| H7 `+85,5` / `write` | Later revised | `@param mixed $string; @return void; @throws BadMethodCallException` | `@param string $string; @return int; @throws BadMethodCallException` |

### `src/IO/Psr7/ZlibFilteredStream.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+14,5` / `__construct` | Later revised | `@param StreamInterface $stream; @param int $zlibCoding; @return void` | `@param StreamInterface $stream; @param int $zlibCoding` |
| H2 `+24,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H3 `+32,4` / `processPayload` | Corrected now | `@param string $data; @return string` | `@param string $data; @return string; @throws \RuntimeException` |
| H4 `+40,3` / `processFooter` | Corrected now | `@return string` | `@return string; @throws \RuntimeException` |

### `src/IO/Psr7/ZlibFilteredStreamFactory.php`

Compared PSR 1.0/1.1, StreamDecoratorTrait and native bodies. Preserve null sizes/detach, scalar metadata and exception-only methods. 7316cf2 already repaired inferred returns; restore filtered-stream string rewind. Current ^1.1 requirement resolves historical parameter incompatibility.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param int $zlibCoding; @return void` | `@param int $zlibCoding` |
| H2 `+21,4` / `toFilteredStream` | Retained | `@param StreamInterface $stream; @return StreamInterface` | `@param StreamInterface $stream; @return StreamInterface` |

### `src/IO/Readable/ChunkReaderInterface.php`

Setter-style methods return no value, consistent with native DOM/file void methods. No chunk/stream-reader implementations found in Core/Farah; retain their setter contract without adding native restrictions.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `fromChunks` | Retained | `@param Generator $chunks; @return void` | `@param Generator $chunks; @return void` |

### `src/IO/Readable/DOMReaderInterface.php`

Setter-style methods return no value, consistent with native DOM/file void methods. No chunk/stream-reader implementations found in Core/Farah; retain their setter contract without adding native restrictions.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,4` / `fromDocument` | Retained | `@param DOMDocument $sourceDoc; @return void` | `@param DOMDocument $sourceDoc; @return void` |
| H2 `+23,4` / `fromElement` | Retained | `@param DOMElement $sourceElement; @return void` | `@param DOMElement $sourceElement; @return void` |

### `src/IO/Readable/FileReaderInterface.php`

Setter-style methods return no value, consistent with native DOM/file void methods. No chunk/stream-reader implementations found in Core/Farah; retain their setter contract without adding native restrictions.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+16,4` / `fromFile` | Retained | `@param SplFileInfo $sourceFile; @return void` | `@param SplFileInfo $sourceFile; @return void` |
| H2 `+22,4` / `fromString` | Retained | `@param string $sourceString; @return void` | `@param string $sourceString; @return void` |

### `src/IO/Readable/StreamReaderInterface.php`

Setter-style methods return no value, consistent with native DOM/file void methods. No chunk/stream-reader implementations found in Core/Farah; retain their setter contract without adding native restrictions.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `fromStream` | Retained | `@param StreamInterface $stream; @return void` | `@param StreamInterface $stream; @return void` |

### `src/IO/RecursiveFileIterator.php`

Generators expose realpath results including possible false, so broad element types remain deliberate. Invalid root throws; file/directory filters retain existing behavior.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,5` / `iterateDirectoriesAndFiles` | Later revised | `@param string $directory; @return iterable; @throws InvalidArgumentException` | `@param string $directory; @return iterable<mixed>; @throws InvalidArgumentException` |
| H2 `+26,3` / `accept` | Retained | `@return bool` | `@return bool` |
| H3 `+43,4` / `iterateFiles` | Later revised | `@param string $directory; @return iterable` | `@param string $directory; @return iterable<mixed>` |
| H4 `+55,4` / `iterateDirectories` | Later revised | `@param string $directory; @return iterable` | `@param string $directory; @return iterable<mixed>` |

### `src/IO/Sanitizer/ArraySanitizer.php`

Compared all implementations and Farah usage. Interface results are mixed; concrete results retain string/int/array. Array defaults and token array inputs preserve arbitrary keys and values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `__construct` | Later revised | `@param array $default; @return void` | `@param array<mixed> $default` |
| H2 `+18,4` / `apply` | Later revised | `@param mixed $value; @return array` | `@param mixed $value; @return array<mixed>` |
| H3 `+26,3` / `getDefault` | Later revised | `@return array` | `@return array<mixed>` |

### `src/IO/Sanitizer/FileNameSanitizer.php`

Compared all implementations and Farah usage. Interface results are mixed; concrete results retain string/int/array. Array defaults and token array inputs preserve arbitrary keys and values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+12,4` / `__construct` | Later revised | `@param string $default; @return void` | `@param string $default` |
| H2 `+20,4` / `apply` | Retained | `@param mixed $value; @return string` | `@param mixed $value; @return string` |
| H3 `+29,3` / `getDefault` | Retained | `@return string` | `@return string` |

### `src/IO/Sanitizer/IntegerSanitizer.php`

Compared all implementations and Farah usage. Interface results are mixed; concrete results retain string/int/array. Array defaults and token array inputs preserve arbitrary keys and values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `__construct` | Later revised | `@param int $default; @return void` | `@param int $default` |
| H2 `+18,4` / `apply` | Retained | `@param mixed $value; @return int` | `@param mixed $value; @return int` |
| H3 `+27,3` / `getDefault` | Retained | `@return int` | `@return int` |

### `src/IO/Sanitizer/SanitizerInterface.php`

Compared all implementations and Farah usage. Interface results are mixed; concrete results retain string/int/array. Array defaults and token array inputs preserve arbitrary keys and values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,4` / `apply` | Corrected now | `@param mixed $value; @return void` | `@param mixed $value; @return mixed` |
| H2 `+14,3` / `getDefault` | Corrected now | `@return void` | `@return mixed` |

### `src/IO/Sanitizer/StringSanitizer.php`

Compared all implementations and Farah usage. Interface results are mixed; concrete results retain string/int/array. Array defaults and token array inputs preserve arbitrary keys and values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `__construct` | Later revised | `@param string $default; @return void` | `@param string $default` |
| H2 `+18,4` / `apply` | Retained | `@param mixed $value; @return string` | `@param mixed $value; @return string` |
| H3 `+27,3` / `getDefault` | Retained | `@return string` | `@return string` |

### `src/IO/Sanitizer/TokenSanitizer.php`

Compared all implementations and Farah usage. Interface results are mixed; concrete results retain string/int/array. Array defaults and token array inputs preserve arbitrary keys and values.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `__construct` | Later revised | `@param array $default; @return void` | `@param array<mixed> $default` |
| H2 `+18,4` / `apply` | Later revised | `@param mixed $value; @return array` | `@param mixed $value; @return array<mixed>` |
| H3 `+39,3` / `getDefault` | Later revised | `@return array` | `@return array<mixed>` |

### `src/IO/Writable/Adapter/ChunkWriterFromFileWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+18,5` / `__construct` | Later revised | `@param FileWriterInterface $source; @param int $chunkSize; @return void` | `@param FileWriterInterface $source; @param int $chunkSize` |
| H2 `+28,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<int, string\|false>` |

### `src/IO/Writable/Adapter/ChunkWriterFromGenerator.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param Generator $generator; @return void` | `@param Generator $generator` |
| H2 `+21,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<mixed, mixed>` |

### `src/IO/Writable/Adapter/ChunkWriterFromProcess.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+14,4` / `__construct` | Later revised | `@param Process $process; @return void` | `@param Process $process` |
| H2 `+22,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<int, string>` |

### `src/IO/Writable/Adapter/ChunkWriterFromStreamWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,5` / `__construct` | Later revised | `@param StreamWriterInterface $source; @param int $chunkSize; @return void` | `@param StreamWriterInterface $source; @param int $chunkSize` |
| H2 `+27,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<int, string>` |

### `src/IO/Writable/Adapter/ChunkWriterFromStringWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+14,4` / `__construct` | Later revised | `@param StringWriterInterface $source; @return void` | `@param StringWriterInterface $source` |
| H2 `+22,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<int, string>` |

### `src/IO/Writable/Adapter/DOMWriterFromFileWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+21,6` / `__construct` | Later revised | `@param FileWriterInterface $source; @param string\|null $documentURI; @param bool $isHtml; @return void` | `@param FileWriterInterface $source; @param ?string $documentURI; @param bool $isHtml` |
| H2 `+33,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/IO/Writable/Adapter/DOMWriterFromStringWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+21,6` / `__construct` | Later revised | `@param StringWriterInterface $source; @param string\|null $documentURI; @param bool $isHtml; @return void` | `@param StringWriterInterface $source; @param ?string $documentURI; @param bool $isHtml` |
| H2 `+33,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/IO/Writable/Adapter/FileWriterFromStringWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Later revised | `@param StringWriterInterface $source; @return void` | `@param StringWriterInterface $source` |
| H2 `+23,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |

### `src/IO/Writable/Adapter/StreamWriterFromChunkWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+18,5` / `__construct` | Later revised | `@param ChunkWriterInterface $source; @param bool $canLoadAllChunks; @return void` | `@param ChunkWriterInterface $source; @param bool $canLoadAllChunks` |
| H2 `+28,3` / `toStream` | Retained | `@return StreamInterface` | `@return StreamInterface` |

### `src/IO/Writable/Adapter/StreamWriterFromFileWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Later revised | `@param FileWriterInterface $source; @return void` | `@param FileWriterInterface $source` |
| H2 `+23,3` / `toStream` | Retained | `@return StreamInterface` | `@return StreamInterface` |

### `src/IO/Writable/Adapter/StreamWriterFromStringWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Later revised | `@param StringWriterInterface $source; @return void` | `@param StringWriterInterface $source` |
| H2 `+23,3` / `toStream` | Retained | `@return StreamInterface` | `@return StreamInterface` |

### `src/IO/Writable/Adapter/StringWriterFromChunkWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param ChunkWriterInterface $source; @return void` | `@param ChunkWriterInterface $source` |
| H2 `+21,3` / `toString` | Retained | `@return string` | `@return string` |

### `src/IO/Writable/Adapter/StringWriterFromDOMWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param DOMWriterInterface $source; @return void` | `@param DOMWriterInterface $source` |
| H2 `+21,3` / `toString` | Corrected now | `@return string` | `@return string; @throws \RuntimeException` |

### `src/IO/Writable/Adapter/StringWriterFromFileWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param FileWriterInterface $source; @return void` | `@param FileWriterInterface $source` |
| H2 `+21,3` / `toString` | Corrected now | `@return string` | `@return string; @throws \RuntimeException` |

### `src/IO/Writable/Adapter/StringWriterFromStreamWriter.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param StreamWriterInterface $source; @return void` | `@param StreamWriterInterface $source` |
| H2 `+21,3` / `toString` | Retained | `@return string` | `@return string` |

### `src/IO/Writable/Decorators/ChunkWriterFileCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+18,6` / `__construct` | Later revised | `@param ChunkWriterInterface $sourceWriter; @param SplFileInfo $cacheFile; @param callable\|null $shouldRefreshCacheDelegate; @return void` | `@param ChunkWriterInterface $sourceWriter; @param SplFileInfo $cacheFile; @param ?callable $shouldRefreshCacheDelegate` |
| H2 `+29,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<mixed, mixed>` |
| H3 `+51,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |

### `src/IO/Writable/Decorators/ChunkWriterMemoryCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Later revised | `@param ChunkWriterInterface $source; @return void` | `@param ChunkWriterInterface $source` |
| H2 `+23,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<mixed, mixed>` |

### `src/IO/Writable/Decorators/DOMWriterFileCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+21,6` / `__construct` | Later revised | `@param DOMWriterInterface $sourceWriter; @param SplFileInfo $cacheFile; @param callable\|null $shouldRefreshCacheDelegate; @return void` | `@param DOMWriterInterface $sourceWriter; @param SplFileInfo $cacheFile; @param ?callable $shouldRefreshCacheDelegate` |
| H2 `+32,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |
| H3 `+40,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/IO/Writable/Decorators/DOMWriterMemoryCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,4` / `__construct` | Later revised | `@param DOMWriterInterface $source; @return void` | `@param DOMWriterInterface $source` |
| H2 `+25,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/IO/Writable/Decorators/FileWriterFileCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+14,6` / `__construct` | Later revised | `@param FileWriterInterface $sourceWriter; @param SplFileInfo $cacheFile; @param callable\|null $shouldRefreshCacheDelegate; @return void` | `@param FileWriterInterface $sourceWriter; @param SplFileInfo $cacheFile; @param ?callable $shouldRefreshCacheDelegate` |
| H2 `+25,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |

### `src/IO/Writable/Decorators/FileWriterMemoryCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Later revised | `@param FileWriterInterface $source; @return void` | `@param FileWriterInterface $source` |
| H2 `+23,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |

### `src/IO/Writable/Decorators/StreamWriterMemoryCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Later revised | `@param StreamWriterInterface $source; @return void` | `@param StreamWriterInterface $source` |
| H2 `+23,3` / `toStream` | Retained | `@return StreamInterface` | `@return StreamInterface` |

### `src/IO/Writable/Decorators/StringWriterMemoryCache.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+14,4` / `__construct` | Later revised | `@param StringWriterInterface $source; @return void` | `@param StringWriterInterface $source` |
| H2 `+22,3` / `toString` | Retained | `@return string` | `@return string` |

### `src/IO/Writable/Delegates/ChunkWriterFromChunkWriterDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+16,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): ChunkWriterInterface $delegate` |
| H2 `+24,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<mixed, mixed>` |

### `src/IO/Writable/Delegates/ChunkWriterFromChunksDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+16,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): Generator<mixed, mixed> $delegate` |
| H2 `+24,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<mixed, mixed>` |

### `src/IO/Writable/Delegates/DOMWriterFromDOMWriterDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): DOMWriterInterface $delegate` |
| H2 `+25,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |
| H3 `+32,4` / `toElement` | Retained | `@param DOMDocument $targetDoc; @return DOMElement` | `@param DOMDocument $targetDoc; @return DOMElement` |

### `src/IO/Writable/Delegates/DOMWriterFromDocumentDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+18,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): DOMDocument $delegate` |
| H2 `+26,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/IO/Writable/Delegates/DOMWriterFromElementDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(DOMDocument): DOMElement $delegate` |
| H2 `+27,4` / `toElement` | Retained | `@param DOMDocument $targetDoc; @return DOMElement` | `@param DOMDocument $targetDoc; @return DOMElement` |

### `src/IO/Writable/Delegates/FileWriterFromFileDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+16,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): SplFileInfo $delegate` |
| H2 `+24,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |

### `src/IO/Writable/Delegates/StreamWriterFromStreamDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+16,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): StreamInterface $delegate` |
| H2 `+24,3` / `toStream` | Retained | `@return StreamInterface` | `@return StreamInterface` |

### `src/IO/Writable/Delegates/StringWriterFromStringDelegate.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+15,4` / `__construct` | Corrected now | `@param callable $delegate; @return void` | `@param callable(): string $delegate` |
| H2 `+23,3` / `toString` | Retained | `@return string` | `@return string` |

### `src/IO/Writable/Mergers/ChunkWriterMerger.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param ChunkWriterInterface ...$writers; @return void` | `@param ChunkWriterInterface ...$writers` |
| H2 `+21,3` / `toChunks` | Corrected now | `@return Generator` | `@return Generator<mixed, mixed>` |

### `src/IO/Writable/Traits/DOMWriterDocumentFromElementTrait.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+16,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/IO/Writable/Traits/DOMWriterElementFromDocumentTrait.php`

Compared writer interfaces, adapter/delegate bodies and traits. Constructors remain without return tags. Preserve generator keys/values, fread string|false values, delegate callable input/output, and DOM/file/stream outputs. Keep later serialization/read/import guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+18,5` / `toElement` | Retained | `@param DOMDocument $targetDoc; @return DOMElement; @throws RuntimeException` | `@param DOMDocument $targetDoc; @return DOMElement; @throws RuntimeException` |

### `src/Image.php`

Compared GD and external-tool branches. GD results vary between PHP 7.4 resources and PHP 8 GdImage objects, with false load results; retain flexible native API and describe this explicitly. Disabled generateThumbnail was later removed; do not restore.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+49,3` / `getTempFile` | Retained | `@return string` | `@return string` |
| H2 `+59,9` / `createSprite` | Later revised | `@param mixed $destFile; @param mixed $spriteWidth; @param mixed $spriteHeight; @param mixed $cols; @param mixed $rows; @param array $imageList; @return bool` | `@param mixed $destFile; @param mixed $spriteWidth; @param mixed $spriteHeight; @param mixed $cols; @param mixed $rows; @param array<mixed> $imageList; @return bool` |
| H3 `+78,7` / `addSprite` | Later revised | `@param mixed $destFile; @param array $sourceFileList; @param mixed $spriteWidth; @param mixed $spriteHeigh; @return bool` | `@param mixed $destFile; @param array<mixed> $sourceFileList; @param mixed $spriteWidth; @param mixed $spriteHeigh; @return bool` |
| H4 `+111,4` / `imageInfo` | Corrected now | `@param mixed $file; @return array` | `@param mixed $file; @return array<mixed>; @throws DomainException` |
| H5 `+124,5` / `createFromFile` | Corrected now | `@param mixed $file; @return mixed; @throws DomainException` | `@param mixed $file; @return mixed A GD resource on PHP 7.4, GdImage on PHP 8+, or false on load failure.; @throws DomainException` |
| H6 `+143,5` / `setTransparency` | Corrected now | `@param mixed $image_source; @param mixed $new_image; @return void` | `@param mixed $image_source GD resource on PHP 7.4 or GdImage on PHP 8+.; @param mixed $new_image GD resource on PHP 7.4 or GdImage on PHP 8+.; @return void; @throws DomainException` |
| H7 `+165,8` / `splitImage` | Corrected now | `@param mixed $file; @param mixed $x; @param mixed $y; @param mixed $new_width; @param mixed $new_height; @return mixed` | `@param mixed $file; @param mixed $x; @param mixed $y; @param mixed $new_width; @param mixed $new_height; @return mixed A GD resource on PHP 7.4, GdImage on PHP 8+, or false on load failure.; @throws DomainException` |
| H8 `+190,5` / `convertFile` | Retained | `@param mixed $sourceFile; @param mixed $destFile; @return bool` | `@param mixed $sourceFile; @param mixed $destFile; @return bool` |
| H9 `+220,7` / `scaleFile` | Retained | `@param mixed $sourceFile; @param mixed $destFile; @param mixed $width; @param mixed $height; @return bool` | `@param mixed $sourceFile; @param mixed $destFile; @param mixed $width; @param mixed $height; @return bool` |
| H10 `+249,7` / `cropFile` | Retained | `@param mixed $sourceFile; @param mixed $destFile; @param mixed $width; @param mixed $height; @return void` | `@param mixed $sourceFile; @param mixed $destFile; @param mixed $width; @param mixed $height; @return void` |
| H11 `+259,6` / `mergeFile` | Retained | `@param mixed $sourceFile; @param mixed $appendFile; @param mixed $targetFile; @return bool` | `@param mixed $sourceFile; @param mixed $appendFile; @param mixed $targetFile; @return bool` |
| H12 `+288,8` / `generateThumbnail` | Removed later | `@param mixed $sourceFile; @param mixed $thumbWidth; @param mixed $thumbHeight; @param mixed $returnLink; @return mixed; @throws Exception` | `(method removed)` |

### `src/ImageHelper.php`

Conversion/sprite methods are void; Imagick/GD inputs remain native. Document explicit PNG load failure.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,6` / `convertToPng` | Corrected now | `@param SplFileInfo $sourceFile; @param SplFileInfo $targetFile; @param int\|null $alphaColorIndex; @return void` | `@param SplFileInfo $sourceFile; @param SplFileInfo $targetFile; @param ?int $alphaColorIndex; @return void; @throws \RuntimeException` |
| H2 `+39,9` / `createSpriteSheetFromImages` | Retained | `@param SplFileInfo $targetFile; @param int $spriteWidth; @param int $spriteHeight; @param int $columns; @param int $rows; @param Imagick ...$sprites; @return void` | `@param SplFileInfo $targetFile; @param int $spriteWidth; @param int $spriteHeight; @param int $columns; @param int $rows; @param Imagick ...$sprites; @return void` |
| H3 `+69,9` / `createSpriteSheet` | Retained | `@param SplFileInfo $targetFile; @param int $spriteWidth; @param int $spriteHeight; @param int $columns; @param int $rows; @param SplFileInfo ...$spriteFiles; @return void` | `@param SplFileInfo $targetFile; @param int $spriteWidth; @param int $spriteHeight; @param int $columns; @param int $rows; @param SplFileInfo ...$spriteFiles; @return void` |

### `src/InterExec.php`

Callback return stays mixed, setters/run loops discard values, run returns the receiver. Environment default permits null. Later run_shutdown:void matches its body.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+115` / `__construct` | Corrected now | `@param string $command_to_run; @param array $environment_vars; @return void` | `@param string $command_to_run; @param array<mixed>\|null $environment_vars` |
| H2 `+129` / `on` | Retained | `@param string $event; @param callable $callback; @return void` | `@param string $event; @param callable $callback; @return void` |
| H3 `+144` / `fire` | Later revised | `@param string $event; @param array $args; @return mixed Value resulting from event callback.; @throws InvalidArgumentException` | `@param string $event; @param mixed $args; @return mixed Value resulting from event callback.; @throws InvalidArgumentException` |
| H4 `+196,3` / `run_startup` | Corrected now | `@return void` | `@return void; @throws \RuntimeException` |
| H5 `+232,3` / `run_mainloop` | Corrected now | `@return void` | `@return void; @throws \RuntimeException` |
| H6 `+326,3` / `run_shutdown` | Later revised | `@return mixed` | `@return void` |

### `src/MimeTypeDictionary.php`

Lookup fallbacks are empty extension/compression strings or application/octet-stream; classifiers return boolean.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+51,4` / `guessExtension` | Retained | `@param string $mime; @return string` | `@param string $mime; @return string` |
| H2 `+63,4` / `guessMime` | Retained | `@param string $extension; @return string` | `@param string $extension; @return string` |
| H3 `+75,4` / `guessCompressions` | Retained | `@param string $mime; @return string` | `@param string $mime; @return string` |
| H4 `+87,5` / `matchesMime` | Retained | `@param string $extension; @param string $testMime; @return bool` | `@param string $extension; @param string $testMime; @return bool` |
| H5 `+105,4` / `isXml` | Retained | `@param string $type; @return bool` | `@param string $type; @return bool` |
| H6 `+113,4` / `isHtml` | Retained | `@param string $type; @return bool` | `@param string $type; @return bool` |
| H7 `+121,4` / `isText` | Retained | `@param string $type; @return bool` | `@param string $type; @return bool` |

### `src/RCon.php`

Packet string fields return strings, getLong returns int. Preserve buffer-consumption fixes, document read/unpack exceptions and reject nonpositive read length.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+57,6` / `__construct` | Later revised | `@param mixed $ip; @param mixed $port; @param mixed $password; @return void` | `@param mixed $ip; @param mixed $port; @param mixed $password` |
| H2 `+77,4` / `execute` | Corrected now | `@param mixed $message; @return mixed` | `@param mixed $message; @return string` |
| H3 `+85,5` / `send` | Corrected now | `@param mixed $commandId; @param mixed $messageBody; @return mixed` | `@param mixed $commandId; @param mixed $messageBody; @return string; @throws Exception` |
| H4 `+140,6` / `throwError` | Later revised | `@param mixed $string; @param array $data; @return void; @throws Exception` | `@param mixed $string; @param array<mixed> $data; @return void; @throws Exception` |
| H5 `+156` / `getLong` | Corrected now | `@param string $string; @return mixed` | `@param string $string; @return int; @throws Exception` |
| H6 `+174` / `getString` | Corrected now | `@param string $string; @return mixed` | `@param string $string; @return string` |

### `src/ServerEnvironment.php`

Configuration getters return resolved directory/host strings; setters discard results. Preserve later deferred directory resolution and defaults.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,4` / `setRootDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H2 `+27,3` / `getRootDirectory` | Retained | `@return string` | `@return string` |
| H3 `+42,4` / `setLogDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H4 `+50,3` / `getLogDirectory` | Retained | `@return string` | `@return string` |
| H5 `+65,4` / `setCacheDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H6 `+73,3` / `getCacheDirectory` | Retained | `@return string` | `@return string` |
| H7 `+88,4` / `setDataDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H8 `+96,3` / `getDataDirectory` | Retained | `@return string` | `@return string` |
| H9 `+111,4` / `setHostName` | Retained | `@param string $value; @return void` | `@param string $value; @return void` |
| H10 `+119,3` / `getHostName` | Retained | `@return string` | `@return string` |

### `src/Storage.php`

Checked cache/HTTP/DBMS branches and logging/touch lifecycle. Nullable cacheTime defaults and mixed JSON results are documented; preserve exception retirement and later serialization guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+36,4` / `setLogEnabled` | Retained | `@param bool $value; @return void` | `@param bool $value; @return void` |
| H2 `+44,3` / `getLogEnabled` | Retained | `@return bool` | `@return bool` |
| H3 `+59,4` / `setLogDirectory` | Retained | `@param string $directory; @return void` | `@param string $directory; @return void` |
| H4 `+67,3` / `getLogDirectory` | Retained | `@return string` | `@return string` |
| H5 `+82,4` / `setTouchOnExit` | Retained | `@param bool $value; @return void` | `@param bool $value; @return void` |
| H6 `+90,3` / `getTouchOnExit` | Retained | `@return bool` | `@return bool` |
| H7 `+121` / `loadExternalDocument` | Corrected now | `@param string $uri; @param int $cacheTime; @param mixed $data; @param mixed $options; @return null\|DOMDocument; @throws Exception` | `@param string $uri; @param ?int $cacheTime; @param mixed $data; @param mixed $options; @return ?DOMDocument; @throws Exception` |
| H8 `+149` / `clearExternalDocument` | Corrected now | `@param string $uri; @param int $cacheTime; @param mixed $data; @param mixed $options; @return boolean; @throws Exception` | `@param string $uri; @param ?int $cacheTime; @param mixed $data; @param mixed $options; @return boolean; @throws Exception` |
| H9 `+166` / `loadExternalXPath` | Corrected now | `@param string $uri; @param int $cacheTime; @param mixed $data; @param mixed $options; @return NULL\|DOMXPath; @throws Exception` | `@param string $uri; @param ?int $cacheTime; @param mixed $data; @param mixed $options; @return ?DOMXPath; @throws Exception` |
| H10 `+183` / `loadExternalJSON` | Corrected now | `@param string $uri; @param int $cacheTime; @param mixed $data; @param mixed $options; @return NULL\|mixed; @throws Exception` | `@param string $uri; @param ?int $cacheTime; @param mixed $data; @param mixed $options; @return mixed; @throws Exception` |
| H11 `+208` / `loadExternalFile` | Corrected now | `@param string $uri; @param int $cacheTime; @param mixed $data; @param mixed $options; @return NULL\|string; @throws Exception` | `@param string $uri; @param ?int $cacheTime; @param mixed $data; @param mixed $options; @return ?string; @throws Exception` |
| H12 `+242` / `loadExternalHeader` | Corrected now | `@param string $uri; @param int $cacheTime; @param mixed $data; @param mixed $options; @return NULL\|array; @throws Exception` | `@param string $uri; @param ?int $cacheTime; @param mixed $data; @param mixed $options; @return ?array<mixed>; @throws Exception` |
| H13 `+273,7` / `_httpRequest` | Later revised | `@param array $options; @param mixed $uri; @param mixed $data; @return XMLHttpRequest; @throws Exception` | `@param array<mixed> $options; @param mixed $uri; @param mixed $data; @return XMLHttpRequest; @throws Exception` |
| H14 `+330` / `_httpOptions` | Retained | `@param mixed $options; @return void` | `@param mixed $options; @return void` |
| H15 `+352,5` / `_httpOAuth` | Later revised | `@param array $options; @param string $uri; @return string` | `@param array<mixed> $options; @param string $uri; @return string` |
| H16 `+418,5` / `_getStorageNameFromURI` | Retained | `@param mixed $uri; @return string; @throws Exception` | `@param mixed $uri; @return string; @throws Exception` |
| H17 `+445,4` / `_getHostFromURI` | Retained | `@param mixed $uri; @return mixed` | `@param mixed $uri; @return mixed` |
| H18 `+469,4` / `_getSchemeFromURI` | Retained | `@param mixed $uri; @return mixed` | `@param mixed $uri; @return mixed` |
| H19 `+477,4` / `_hash` | Retained | `@param string $name; @return string` | `@param string $name; @return string` |
| H20 `+485,6` / `_name` | Later revised | `@param array $options; @param mixed $uri; @param mixed $data; @return string` | `@param array<mixed> $options; @param mixed $uri; @param mixed $data; @return string` |
| H21 `+497,3` / `_DOMHelper` | Retained | `@return DOMHelper` | `@return DOMHelper` |
| H22 `+519,4` / `__construct` | Later revised | `@param mixed $storageName; @return void` | `@param mixed $storageName` |
| H23 `+541,3` / `getDBMSTable` | Later revised | `@return mixed` | `@return Table` |
| H24 `+548,3` / `install` | Retained | `@return void` | `@return void` |
| H25 `+611,6` / `retrieveXML` | Later revised | `@param string $name; @param int $modifyTime; @param DOMDocument\|null $targetDoc; @return DOMNode\|null` | `@param string $name; @param int $modifyTime; @param ?DOMDocument $targetDoc; @return ?DOMNode` |
| H26 `+750,4` / `touch` | Retained | `@param int $id; @return void` | `@param int $id; @return void` |
| H27 `+763,3` / `sendTouch` | Retained | `@return void` | `@return void` |
| H28 `+776,3` / `cleanse` | Retained | `@return void` | `@return void` |
| H29 `+785,3` / `cron` | Retained | `@return bool` | `@return bool` |
| H30 `+793,3` / `__destruct` | Retained | `@return void` | `@return void` |
| H31 `+802,6` / `_createLog` | Retained | `@param mixed $method; @param mixed $name; @param mixed $ret; @return void` | `@param mixed $method; @param mixed $name; @param mixed $ret; @return void` |

### `src/StreamFilter/AbstractStreamFilterTest.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,3` / `getInput` | Retained | `@return string` | `@return string` |
| H2 `+15,4` / `calculateExpectedResult` | Retained | `@param string $input; @return string` | `@param string $input; @return string` |
| H3 `+21,3` / `getFilterClass` | Retained | `@return string` | `@return string` |
| H4 `+34,3` / `setUp` | Retained | `@return void` | `@return void` |
| H5 `+46,3` / `testWriteToResource` | Retained | `@return void` | `@return void` |
| H6 `+60,3` / `testWriteToPath` | Retained | `@return void` | `@return void` |
| H7 `+72,3` / `testReadFromResource` | Retained | `@return void` | `@return void` |
| H8 `+86,3` / `testReadFromPath` | Retained | `@return void` | `@return void` |

### `src/StreamFilter/ChunkEncode.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H2 `+15,4` / `processPayload` | Retained | `@param string $input; @return string` | `@param string $input; @return string` |
| H3 `+23,3` / `processFooter` | Retained | `@return string` | `@return string` |

### `src/StreamFilter/Identity.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H2 `+15,4` / `processPayload` | Retained | `@param string $input; @return string` | `@param string $input; @return string` |
| H3 `+23,3` / `processFooter` | Retained | `@return string` | `@return string` |

### `src/StreamFilter/StreamFilterBase.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,3` / `onCreate` | Retained | `@return bool` | `@return bool` |
| H2 `+27,7` / `filter` | Corrected now | `@param mixed $in; @param mixed $out; @param mixed $consumed; @param mixed $closing; @return int` | `@param resource $in; @param resource $out; @param int\|float $consumed; @param bool $closing; @return int` |
| H3 `+68,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H4 `+73,4` / `processPayload` | Retained | `@param string $input; @return string` | `@param string $input; @return string` |
| H5 `+79,3` / `processFooter` | Retained | `@return string` | `@return string` |

### `src/StreamFilter/StreamFilterInterface.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,7` / `filter` | Corrected now | `@param mixed $in; @param mixed $out; @param mixed $consumed; @param mixed $closing; @return void` | `@param resource $in; @param resource $out; @param int\|float $consumed; @param bool $closing; @return int` |
| H2 `+17,3` / `onClose` | Retained | `@return void` | `@return void` |
| H3 `+22,3` / `onCreate` | Corrected now | `@return void` | `@return bool` |

### `src/StreamFilter/ZlibEncodeBase.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,3` / `getZlibEncoding` | Retained | `@return int` | `@return int` |
| H2 `+15,3` / `processHeader` | Retained | `@return string` | `@return string` |
| H3 `+23,4` / `processPayload` | Corrected now | `@param string $input; @return string` | `@param string $input; @return string; @throws \RuntimeException` |
| H4 `+31,3` / `processFooter` | Corrected now | `@return string` | `@return string; @throws \RuntimeException` |

### `src/StreamFilter/ZlibEncodeDeflate.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,3` / `getZlibEncoding` | Retained | `@return int` | `@return int` |

### `src/StreamFilter/ZlibEncodeGzip.php`

Compared php_user_filter callbacks, concrete header/payload/footer and abstract test hooks. filter returns integer status, onCreate boolean; actual lifecycle/test methods remain void. Brigade inputs are resources, consumed is numeric by reference. Preserve compression-failure guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+8,3` / `getZlibEncoding` | Retained | `@return int` | `@return int` |

### `src/StreamWrapper/FileStreamWrapper.php`

Constructor must supply a resource to its parent; failed opening now throws via tryFopen rather than passing false as a handle.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `__construct` | Corrected now | `@param SplFileInfo $file; @return void` | `@param SplFileInfo $file; @throws \RuntimeException` |

### `src/StreamWrapper/Psr7StreamWrapper.php`

PSR exceptions convert to false or -1 for package-specific seek status. Preserve close/stat/delegation contracts.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+13,4` / `__construct` | Later revised | `@param StreamInterface $stream; @return void` | `@param StreamInterface $stream` |
| H2 `+21,3` / `stream_stat` | Later revised | `@return array` | `@return array<mixed>` |
| H3 `+28,3` / `stream_eof` | Retained | `@return bool` | `@return bool` |
| H4 `+35,5` / `stream_seek` | Retained | `@param int $offset; @param int $whence; @return int` | `@param int $offset; @param int $whence; @return int` |
| H5 `+49,4` / `stream_read` | Later revised | `@param int $count; @return mixed` | `@param int $count; @return string\|false` |
| H6 `+61,4` / `stream_write` | Later revised | `@param string $data; @return mixed` | `@param string $data; @return int\|false` |
| H7 `+73,3` / `stream_tell` | Later revised | `@return mixed` | `@return int\|false` |
| H8 `+84,3` / `stream_close` | Retained | `@return bool` | `@return bool` |

### `src/StreamWrapper/ResourceStreamWrapper.php`

Resource input; stat array/EOF boolean/fseek status/read-write-tell false paths match platform APIs. Existing non-owning close behavior remains.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+10,4` / `__construct` | Corrected now | `@param mixed $resource; @return void` | `@param resource $resource` |
| H2 `+18,3` / `stream_stat` | Corrected now | `@return array` | `@return array<mixed>; @throws \RuntimeException` |
| H3 `+25,3` / `stream_eof` | Retained | `@return bool` | `@return bool` |
| H4 `+32,5` / `stream_seek` | Retained | `@param int $offset; @param int $whence; @return int` | `@param int $offset; @param int $whence; @return int` |
| H5 `+41,4` / `stream_read` | Later revised | `@param int $count; @return mixed` | `@param int $count; @return string\|false` |
| H6 `+49,4` / `stream_write` | Later revised | `@param string $data; @return mixed` | `@param string $data; @return int\|false` |
| H7 `+57,3` / `stream_tell` | Later revised | `@return mixed` | `@return int\|false` |
| H8 `+64,3` / `stream_close` | Retained | `@return bool` | `@return bool` |

### `src/StreamWrapper/StreamWrapperRegistrar.php`

Registration is void and duplicate scheme throws. URL stat can fail; opened_path is by reference. Delegation preserves package stream-wrapper contracts.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+25,6` / `registerStreamWrapper` | Retained | `@param string $scheme; @param StreamWrapperFactoryInterface $factory; @return void; @throws LogicException` | `@param string $scheme; @param StreamWrapperFactoryInterface $factory; @return void; @throws LogicException` |
| H2 `+93` / `stream_stat` | Later revised | `@see StreamWrapperInterface::stream_stat; @return array` | `@return array<mixed>; @see StreamWrapperInterface::stream_stat` |
| H3 `+103` / `stream_eof` | Later revised | `@see StreamWrapperInterface::stream_eof; @return bool` | `@return bool; @see StreamWrapperInterface::stream_eof` |
| H4 `+113,3` / `stream_seek` | Later revised | `@see StreamWrapperInterface::stream_seek; @param int $offset; @param int $whence; @return int` | `@param int $offset; @param int $whence; @return int; @see StreamWrapperInterface::stream_seek` |
| H5 `+125,2` / `stream_read` | Later revised | `@see StreamWrapperInterface::stream_read; @param int $count; @return mixed` | `@param int $count; @return string\|false; @see StreamWrapperInterface::stream_read` |
| H6 `+136,2` / `stream_write` | Later revised | `@see StreamWrapperInterface::stream_write; @param string $data; @return mixed` | `@param string $data; @return int\|false; @see StreamWrapperInterface::stream_write` |
| H7 `+147` / `stream_tell` | Later revised | `@see StreamWrapperInterface::stream_tell; @return mixed` | `@return int\|false; @see StreamWrapperInterface::stream_tell` |
| H8 `+157` / `stream_close` | Later revised | `@see StreamWrapperInterface::stream_close; @return bool` | `@return bool; @see StreamWrapperInterface::stream_close` |

### `src/WebCrawler.php`

crawl joins a string; crawlURL mutates state and is void. buildURL has string and false paths; retain flexible legacy inputs.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+29,4` / `__construct` | Later revised | `@param mixed $url; @return void` | `@param mixed $url` |
| H2 `+39,3` / `crawl` | Retained | `@return string` | `@return string` |
| H3 `+57,6` / `crawlURL` | Retained | `@param mixed $url; @param mixed $depth; @param mixed $parentUrl; @return void` | `@param mixed $url; @param mixed $depth; @param mixed $parentUrl; @return void` |
| H4 `+127,5` / `buildURL` | Retained | `@param mixed $url; @param mixed $parentUrl; @return mixed` | `@param mixed $url; @param mixed $parentUrl; @return mixed` |

### `src/XML/LeanElement.php`

Tree lists contain LeanElement values; attribute/default contents remain flexible. Preserve child iteration and serialization defaults; document missing-root exception.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+22,4` / `createTreeListFromDOMNodeList` | Corrected now | `@param DOMNodeList $domNodeList; @return array` | `@param DOMNodeList<\DOMNode> $domNodeList; @return list<LeanElement>` |
| H2 `+36,4` / `createTreeFromDOMDocument` | Corrected now | `@param DOMDocument $domDocument; @return LeanElement` | `@param DOMDocument $domDocument; @return LeanElement; @throws \InvalidArgumentException` |
| H3 `+44,4` / `createTreeFromDOMElement` | Retained | `@param DOMElement $domElement; @return LeanElement` | `@param DOMElement $domElement; @return LeanElement` |
| H4 `+52,5` / `createOneFromDOMElement` | Later revised | `@param DOMElement $element; @param array $children; @return LeanElement` | `@param DOMElement $element; @param array<mixed> $children; @return LeanElement` |
| H5 `+66,6` / `createOneFromArray` | Later revised | `@param string $tag; @param array $attributes; @param iterable $children; @return LeanElement` | `@param string $tag; @param array<mixed> $attributes; @param iterable<mixed> $children; @return LeanElement` |
| H6 `+115` / `getAttribute` | Later revised | `@param string $key; @return mixed; @param mixed $default` | `@param string $key; @param mixed $default; @return mixed` |
| H7 `+125` / `setAttribute` | Retained | `@param string $key; @param mixed $val; @return void` | `@param string $key; @param mixed $val; @return void` |
| H8 `+142` / `appendChild` | Retained | `@param LeanElement $child; @return void` | `@param LeanElement $child; @return void` |
| H9 `+156,4` / `getChildByTag` | Later revised | `@param string $tag; @return LeanElement\|null` | `@param string $tag; @return ?LeanElement` |
| H10 `+169,4` / `toElement` | Retained | `@param DOMDocument $targetDoc; @return DOMElement` | `@param DOMDocument $targetDoc; @return DOMElement` |
| H11 `+184,4` / `withAttributes` | Later revised | `@param array $attributes; @return LeanElement` | `@param array<mixed> $attributes; @return LeanElement` |
| H12 `+196,3` / `__serialize` | Later revised | `@return array` | `@return array<mixed>` |
| H13 `+207,4` / `__unserialize` | Later revised | `@param array $data; @return void` | `@param array<mixed> $data; @return void` |

### `src/XMLHttpRequest.php`

Compared w3c interface and response/header/cookie/event methods. Stateful operations are void; preserve later cURL/header corrections and existing response behavior.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+120,3` / `__construct` | Later revised | `@return void` | `(none)` |
| H2 `+135,8` / `open` | Later revised | `@param string $method; @param string $url; @param bool $async; @param string\|null $user; @param string\|null $password; @return void` | `@param string $method; @param string $url; @param bool $async; @param ?string $user; @param ?string $password; @return void` |
| H3 `+203,6` / `setRequestHeader` | Retained | `@param string $header; @param string $value; @return void; @throws Exception` | `@param string $header; @param string $value; @return void; @throws Exception` |
| H4 `+216,4` / `getRequestHeader` | Retained | `@param mixed $header; @return mixed` | `@param mixed $header; @return mixed` |
| H5 `+225,5` / `send` | Retained | `@param mixed $data; @return void; @throws Exception` | `@param mixed $data; @return void; @throws Exception` |
| H6 `+440,3` / `abort` | Retained | `@return void` | `@return void` |
| H7 `+446,5` / `overrideMimeType` | Retained | `@param string $mime; @return void; @throws Exception` | `@param string $mime; @return void; @throws Exception` |
| H8 `+457,4` / `getResponseHeader` | Retained | `@param string $header; @return string` | `@param string $header; @return string` |
| H9 `+466,3` / `getAllResponseHeaders` | Later revised | `@return array` | `@return array<mixed>` |
| H10 `+474,6` / `addEventListener` | Retained | `@param string $type; @param callable $listener; @param bool $capture; @return void` | `@param string $type; @param callable $listener; @param bool $capture; @return void` |
| H11 `+489,6` / `removeEventListener` | Retained | `@param string $type; @param callable $listener; @param bool $capture; @return void` | `@param string $type; @param callable $listener; @param bool $capture; @return void` |
| H12 `+507,5` / `dispatchEvent` | Retained | `@param mixed $event; @return bool; @throws BadMethodCallException` | `@param mixed $event; @return bool; @throws BadMethodCallException` |
| H13 `+517,4` / `setCookieFile` | Retained | `@param string $file; @return void` | `@param string $file; @return void` |
| H14 `+525,3` / `getCookieFile` | Retained | `@return string` | `@return string` |
| H15 `+533,4` / `fireEventListener` | Retained | `@param mixed $type; @return void` | `@param mixed $type; @return void` |
| H16 `+547,4` / `addCookie` | Retained | `@param mixed $line; @return void` | `@param mixed $line; @return void` |
| H17 `+557,5` / `setCookie` | Retained | `@param mixed $key; @param mixed $val; @return void` | `@param mixed $key; @param mixed $val; @return void` |
| H18 `+566,4` / `parseHeaderList` | Later revised | `@param mixed $head; @return array` | `@param mixed $head; @return array<mixed>` |

### `src/XSLT/Adapters/AdapterInterface.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+18,4` / `setSource` | Retained | `@param InputInterface $input; @return void` | `@param InputInterface $input; @return void` |
| H2 `+24,4` / `setTemplate` | Retained | `@param InputInterface $input; @return void` | `@param InputInterface $input; @return void` |
| H3 `+30,4` / `setParameters` | Later revised | `@param array $param; @return void` | `@param array<mixed> $param; @return void` |
| H4 `+36,4` / `writeFile` | Later revised | `@param SplFileInfo\|null $outputFile; @return SplFileInfo` | `@param ?SplFileInfo $outputFile; @return SplFileInfo` |
| H5 `+42,3` / `writeDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/XSLT/Adapters/CliAdapter.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+23,3` / `__construct` | Later revised | `@param string $path; @param string $args; @return void` | `@param string $path; @param string $args` |
| H2 `+37,3` / `writeFile` | Later revised | `@see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile(); @param SplFileInfo\|null $outputFile; @return SplFileInfo; @throws RuntimeException` | `@param ?SplFileInfo $outputFile; @return SplFileInfo; @throws RuntimeException; @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()` |

### `src/XSLT/Adapters/GenericAdapter.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+24,4` / `setParameters` | Later revised | `@param array $param; @return void` | `@param array<mixed> $param; @return void` |
| H2 `+32,4` / `setSource` | Retained | `@param InputInterface $input; @return void` | `@param InputInterface $input; @return void` |
| H3 `+40,4` / `setTemplate` | Retained | `@param InputInterface $input; @return void` | `@param InputInterface $input; @return void` |
| H4 `+48,3` / `writeDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/XSLT/Adapters/SaxonProcessorAdapter.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+28,2` / `writeFile` | Later revised | `@see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile(); @param SplFileInfo\|null $outputFile; @return SplFileInfo` | `@param ?SplFileInfo $outputFile; @return SplFileInfo; @see \Slothsoft\Core\XSLT\Inputs\InputInterface::toFile()` |

### `src/XSLT/Adapters/XsltProcessorAdapter.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+19,4` / `writeFile` | Later revised | `@param SplFileInfo\|null $outputFile; @return SplFileInfo` | `@param ?SplFileInfo $outputFile; @return SplFileInfo` |
| H2 `+41,3` / `writeDocument` | Corrected now | `@return DOMDocument` | `@return DOMDocument; @throws \RuntimeException` |

### `src/XSLT/Inputs/DocumentInput.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+22,4` / `__construct` | Later revised | `@param DOMDocument $input; @return void` | `@param DOMDocument $input` |
| H2 `+30,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |
| H3 `+40,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/XSLT/Inputs/FileInput.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+22,4` / `__construct` | Later revised | `@param SplFileInfo $input; @return void` | `@param SplFileInfo $input` |
| H2 `+30,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |
| H3 `+37,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/XSLT/Inputs/InputInterface.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+17,3` / `toFile` | Retained | `@return SplFileInfo` | `@return SplFileInfo` |
| H2 `+22,3` / `toDocument` | Retained | `@return DOMDocument` | `@return DOMDocument` |

### `src/XSLT/XsltFactory.php`

Compared InputInterface, GenericAdapter and CLI/libxml/Saxon implementations. Setters are void; outputs are document/file objects or throw. Preserve nullable output file, parameter contents, PHP 7.4 syntax and later document guards.

| Hunk (original new lines) / method | Disposition | Original tags | Current tags |
| --- | --- | --- | --- |
| H1 `+70,5` / `setProcessorForVersion` | Retained | `@param string $xsltVersion; @param string $processorId; @return void` | `@param string $xsltVersion; @param string $processorId; @return void` |
| H2 `+80,4` / `getProcessorForVersion` | Retained | `@param string $xsltVersion; @return string` | `@param string $xsltVersion; @return string` |
| H3 `+89,5` / `setConfigurationForProcessor` | Later revised | `@param string $processorId; @param array $config; @return void` | `@param string $processorId; @param array<mixed> $config; @return void` |
| H4 `+99,4` / `getConfigurationForProcessor` | Later revised | `@param string $processorId; @return array` | `@param string $processorId; @return array<mixed>` |
| H5 `+120,5` / `createAdapter` | Retained | `@param float $xsltVersion; @return AdapterInterface; @throws DomainException` | `@param float $xsltVersion; @return AdapterInterface; @throws DomainException` |
| H6 `+148,5` / `createInput` | Retained | `@param mixed $input; @return InputInterface; @throws DomainException` | `@param mixed $input; @return InputInterface; @throws DomainException` |

## Validation results

- DDEV PHP 8.2, isolated lowest dependencies: 450 tests, 10,711 assertions, pass. Initial root-owned snapshot cache directories needed ownership correction; no package defect.
- DDEV PHP 7.4, installed dependencies: 450 tests, 10,713 assertions, pass.
- DDEV PHP 7.4, isolated Composer prefer-lowest installation: 450 tests, 10,711 assertions, pass. First snapshot run omitted mimeTypes.xml; copying that required package data file resolved those snapshot-only failures.
- Restoring the original interface PHPDocs through temporary external PHPStan stubs makes ContractAnalysis fail with six findings (four void-result uses and two return mismatches). The patched interfaces pass; repository files were not altered for this comparison.
- PHPStan level 8: zero errors after formatting, with installed and lowest permitted dependencies. PhpStorm reformat_file and lint_files: no WARNING/ERROR findings (existing weak suggestions remain).
- Farah PHP 8.2: analysed Asset.php and ParameterFilterStrategy with installed Core and an isolated autoloader override pointing to patched Core. Both report the same nine pre-existing missingType.iterableValue errors; no new errors or Farah file changes. Untyped sanitizer collections limit that analysis, so ContractAnalysis separately supplies a typed SanitizerInterface map matching usage.
- Commit `c948e25`: GitHub's 42 PHP/dependency/OS test combinations passed. Jenkins #375 passed lint and 26 combinations, but both Windows PHP 8.5 variants reported deprecations in the two new reflection-based tests. The follow-up only calls setAccessible() before PHP 8.1, retaining older-PHP support without calling the deprecated API on PHP 8.5. Follow-up CI validation remains pending.
- Windows COM and configured Windows archive/media executables cannot run in Linux DDEV. Static analysis and existence tests do not execute every platform-specific legacy path.
