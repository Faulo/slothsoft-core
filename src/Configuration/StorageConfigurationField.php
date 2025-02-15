<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Configuration;

use Slothsoft\Core\IEphemeralStorage;
use BadMethodCallException;

class StorageConfigurationField extends ConfigurationField {

    public function setValue($newValue) {
        if ($newValue instanceof IEphemeralStorage) {
            $newValue->install();
            parent::setValue($newValue);
        } else {
            throw new BadMethodCallException("Value must be of type IEphemeralStorage!");
        }
    }
}

