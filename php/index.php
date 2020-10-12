<?php

use teamwork\core\application\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application();
$app->start(realpath(__DIR__ . '/src/config.php'));
