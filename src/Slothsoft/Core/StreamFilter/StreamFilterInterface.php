<?php
declare(strict_types = 1);
namespace Slothsoft\Core\StreamFilter;

interface StreamFilterInterface
{

    public function filter($in, $out, &$consumed, $closing);

    public function onClose();

    public function onCreate();
}

