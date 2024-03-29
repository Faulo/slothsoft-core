<?php
declare(strict_types = 1);

use Slothsoft\Core\IO\Memory;

require_once __DIR__ . DIRECTORY_SEPARATOR . sprintf('bootstrap-%s.php', PHP_SAPI);

function my_dump($var) {
    if (! headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8');
    }
    echo '________________________________________________________________________________' . PHP_EOL;
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo '--------------------------------------------------------------------------------' . PHP_EOL;
    var_dump($var);
    echo '________________________________________________________________________________' . PHP_EOL;
}

function temp_file(string $folder, ?string $prefix = null): string {
    $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . normalize_slashes($folder);
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
    for ($i = 0; $i < 10; $i ++) {
        $ret = $path . DIRECTORY_SEPARATOR . uniqid((string) $prefix, true) . '.tmp';
        if (! file_exists($ret)) {
            return $ret;
        }
    }
    throw new \Exception(sprintf('Could not create temporary file at "%s" D:', $path));
}

function temp_dir(string $folder, ?string $prefix = null): string {
    $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . normalize_slashes($folder);
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
    for ($i = 0; $i < 10; $i ++) {
        $ret = $path . DIRECTORY_SEPARATOR . uniqid((string) $prefix, true);
        if (! file_exists($ret) and mkdir($ret, 0777)) {
            return $ret;
        }
    }
    throw new \Exception(sprintf('Could not create temporary directory at "%s" D:', $path));
}

function normalize_slashes(string $path): string {
    return str_replace([
        '/',
        '\\'
    ], DIRECTORY_SEPARATOR, $path);
}

function get_execution_time(): int {
    static $microtime_start = null;
    if ($microtime_start === null) {
        $microtime_start = microtime(true);
        return 0;
    }
    return (int) (1000 * (microtime(true) - $microtime_start));
}
get_execution_time();

function log_execution_time(string $file, int $line): void {
    if (isset($_REQUEST['dev-time'])) {
        static $previousMemory = null;
        static $previousTime = null;

        $nowMemory = memory_get_usage();
        $nowTime = get_execution_time();

        $diffMemory = $previousMemory === null ? 0 : $nowMemory - $previousMemory;
        $diffTime = $previousTime === null ? 0 : $nowTime - $previousTime;

        $previousMemory = $nowMemory;
        $previousTime = $nowTime;

        printf('Took %5dMB (+%5dMB) and %6dms (+%4dms) to get to line %4d in file "%s"%s', $nowMemory / Memory::ONE_MEGABYTE, $diffMemory / Memory::ONE_MEGABYTE, $nowTime, $diffTime, $line, basename($file), PHP_EOL);
        flush();
    }
}
log_execution_time(__FILE__, __LINE__);

function print_execution_time(bool $echo = true): string {
    $ret = sprintf('Execution so far has taken %d ms and %.2f MB.%s', get_execution_time(), memory_get_peak_usage() / 1048576, PHP_EOL);
    if ($echo) {
        echo $ret;
    }
    return $ret;
}

function log_message(string $message) {
    static $file;
    if ($file === null) {
        $file = 'C:\\log.txt';
        file_put_contents($file, '');
    }
    file_put_contents($file, $message . PHP_EOL, FILE_APPEND);
}
