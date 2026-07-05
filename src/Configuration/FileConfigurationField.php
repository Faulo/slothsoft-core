<?php
declare(strict_types = 1);

namespace Slothsoft\Core\Configuration;

use BadMethodCallException;

final class FileConfigurationField extends ConfigurationField {
    
    /**
     * @param mixed $newValue
     * @return void
     * @throws BadMethodCallException
     */
    public function setValue($newValue) {
        $newValue = (string) $newValue;
        if ($newValue === '') {
            throw new BadMethodCallException("Value must be a valid file path!");
        }
        parent::setValue($newValue);
    }
}
