<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Configuration;

use BadMethodCallException;
use RuntimeException;


class FileConfigurationField extends ConfigurationField
{
    public function setValue($newValue) {
        $newValue = (string) $newValue;
        if ($newValue === '') {
            throw new BadMethodCallException("Value must be a valid file path!");
        }
        if (!is_file($newValue)) {
            throw new BadMethodCallException("Value must be a valid file path: $newValue");
        }
        parent::setValue($newValue);
    }
}

