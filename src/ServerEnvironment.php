<?php
declare(strict_types = 1);

namespace Slothsoft\Core;

use Slothsoft\Core\Configuration\ConfigurationField;
use Slothsoft\Core\Configuration\DirectoryConfigurationField;

final class ServerEnvironment {
    
    private static function rootDirectory(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir());
        }
        return $field;
    }
    
    /**
     * @param string $directory
     * @return void
     */
    public static function setRootDirectory(string $directory) {
        self::rootDirectory()->setValue($directory);
    }
    
    /**
     * @return string
     */
    public static function getRootDirectory(): string {
        return self::rootDirectory()->getValue();
    }
    
    private static function logDirectory(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir() . DIRECTORY_SEPARATOR . '.farah-log');
        }
        return $field;
    }
    
    /**
     * @param string $directory
     * @return void
     */
    public static function setLogDirectory(string $directory) {
        self::logDirectory()->setValue($directory);
    }
    
    /**
     * @return string
     */
    public static function getLogDirectory(): string {
        return self::logDirectory()->getValue();
    }
    
    private static function cacheDirectory(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir() . DIRECTORY_SEPARATOR . '.farah-cache');
        }
        return $field;
    }
    
    /**
     * @param string $directory
     * @return void
     */
    public static function setCacheDirectory(string $directory) {
        self::cacheDirectory()->setValue($directory);
    }
    
    /**
     * @return string
     */
    public static function getCacheDirectory(): string {
        return self::cacheDirectory()->getValue();
    }
    
    private static function dataDirectory(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir() . DIRECTORY_SEPARATOR . '.farah-data');
        }
        return $field;
    }
    
    /**
     * @param string $directory
     * @return void
     */
    public static function setDataDirectory(string $directory) {
        self::dataDirectory()->setValue($directory);
    }
    
    /**
     * @return string
     */
    public static function getDataDirectory(): string {
        return self::dataDirectory()->getValue();
    }
    
    private static function hostName(): ConfigurationField {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField('localhost');
        }
        return $field;
    }
    
    /**
     * @param string $value
     * @return void
     */
    public static function setHostName(string $value) {
        self::hostName()->setValue($value);
    }
    
    /**
     * @return string
     */
    public static function getHostName(): string {
        return self::hostName()->getValue();
    }
}
