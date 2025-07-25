<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Configuration;

class ConfigurationField {

    private $value;

    public function __construct($defaultValue = null) {
        if ($defaultValue !== null) {
            $this->setValue($defaultValue);
        }
    }

    public function getValue() {
        if ($this->value === null) {
            $traceList = debug_backtrace();
            $class = $traceList[1]['class'];
            $type = $traceList[1]['type'];
            $function = $traceList[1]['function'];
            $function[0] = 's';
            $param = new \ReflectionParameter([
                $class,
                $function
            ], 0);
            throw new ConfigurationRequiredException(sprintf('Please call %s%s%s($%s) in your config.php.', $class, $type, $function, $param->name));
        }
        return $this->value;
    }

    public function setValue($newValue) {
        $this->value = $newValue;
    }

    public function hasValue(): bool {
        return $this->value !== null;
    }
}

