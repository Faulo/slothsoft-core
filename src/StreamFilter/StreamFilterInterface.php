<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

interface StreamFilterInterface {
    
    /**
     * @param resource $in
     * @param resource $out
     * @param int|float $consumed
     * @param bool $closing
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing);
    
    /**
     * @return void
     */
    public function onClose();
    
    /**
     * @return bool
     */
    public function onCreate();
}

