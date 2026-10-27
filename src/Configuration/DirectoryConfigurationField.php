<?php
declare(strict_types = 1);

namespace Slothsoft\Core\Configuration;

use BadMethodCallException;
use RuntimeException;

final class DirectoryConfigurationField extends ConfigurationField {
    
    /**
     * @param mixed $newValue
     * @return void
     * @throws BadMethodCallException
     */
    public function setValue($newValue) {
        $newValue = (string) $newValue;
        if ($newValue === '') {
            throw new BadMethodCallException("Value must be a valid directory path!");
        }
        parent::setValue($newValue);
    }
    
    /**
     * @return string
     * @throws ConfigurationRequiredException
     * @throws BadMethodCallException
     * @throws RuntimeException
     */
    public function getValue(): string {
        $newValue = parent::getValue();
        if (! is_dir($newValue)) {
            if (is_file($newValue)) {
                throw new BadMethodCallException("Value must be a valid directory path, file supplied: $newValue");
            }
            mkdir($newValue, 0777, true);
        }
        $value = realpath($newValue);
        if ($value === false) {
            throw new RuntimeException("Could not create directory: $newValue");
        }
        parent::setValue($value);
        return $value;
    }
}
