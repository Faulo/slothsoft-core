<?php
namespace Slothsoft\Farah;

use Slothsoft\Core\Storage;

echo 'Storage->cron: ';
$storage = new Storage();
$res = $storage->cron();
echo $res ? 'success!' : 'failure!';