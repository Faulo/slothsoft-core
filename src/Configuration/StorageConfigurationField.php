<?php
declare(strict_types = 1);
namespace Slothsoft\Core\Configuration;

use Slothsoft\Core\EphemeralStorageInterface;
use BadMethodCallException;

class StorageConfigurationField extends ConfigurationField {
    
    public function setValue($newValue) {
        if ($newValue instanceof EphemeralStorageInterface) {
            parent::setValue($newValue);
        } else {
            throw new BadMethodCallException("Value must be of type EphemeralStorageInterface!");
        }
    }
}

