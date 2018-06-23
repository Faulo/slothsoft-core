<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use Slothsoft\Core\Configuration\ConfigurationField;
use Slothsoft\Core\Configuration\DirectoryConfigurationField;

class ServerEnvironment
{

    private static function rootDirectory(): ConfigurationField
    {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir());
        }
        return $field;
    }

    public static function setRootDirectory(string $directory)
    {
        self::rootDirectory()->setValue($directory);
    }

    public static function getRootDirectory(): string
    {
        return self::rootDirectory()->getValue();
    }

    private static function logDirectory(): ConfigurationField
    {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir());
        }
        return $field;
    }

    public static function setLogDirectory(string $directory)
    {
        self::logDirectory()->setValue($directory);
    }

    public static function getLogDirectory(): string
    {
        return self::logDirectory()->getValue();
    }

    private static function cacheDirectory(): ConfigurationField
    {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir());
        }
        return $field;
    }

    public static function setCacheDirectory(string $directory)
    {
        self::cacheDirectory()->setValue($directory);
    }

    public static function getCacheDirectory(): string
    {
        return self::cacheDirectory()->getValue();
    }

    private static function dataDirectory(): ConfigurationField
    {
        static $field;
        if ($field === null) {
            $field = new DirectoryConfigurationField(sys_get_temp_dir());
        }
        return $field;
    }

    public static function setDataDirectory(string $directory)
    {
        self::dataDirectory()->setValue($directory);
    }

    public static function getDataDirectory(): string
    {
        return self::dataDirectory()->getValue();
    }

    private static function hostName(): ConfigurationField
    {
        static $field;
        if ($field === null) {
            $field = new ConfigurationField('localhost');
        }
        return $field;
    }

    public static function setHostName(string $value)
    {
        self::hostName()->setValue($value);
    }

    public static function getHostName(): string
    {
        return self::hostName()->getValue();
    }
}

