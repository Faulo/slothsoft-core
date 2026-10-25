<?php
declare(strict_types = 1);

header('Content-Type: text/plain; charset=UTF-8');
echo $_SERVER['HTTP_X_TEST'] ?? '';
