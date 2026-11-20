<?php
declare(strict_types = 1);

use Slothsoft\Core\FileSystem;

require __DIR__ . '/../../vendor/autoload.php';

FileSystem::download($_SERVER['REQUEST_URI'] === '/empty' ? 'empty.bin' : 'data.bin', 'fixture.bin');
