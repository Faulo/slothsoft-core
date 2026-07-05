<?php
declare(strict_types = 1);

namespace Slothsoft\Core\StreamFilter;

interface StreamFilterInterface {
    
    /**
     * @param mixed $in
     * @param mixed $out
     * @param mixed $consumed
     * @param mixed $closing
     * @return void
     */
    public function filter($in, $out, &$consumed, $closing);
    
    /**
     * @return void
     */
    public function onClose();
    
    /**
     * @return void
     */
    public function onCreate();
}

