<?php

use teamwork\core\App;

require __DIR__ . '/vendor/autoload.php';

try {
    $app = new App();
    $app->start(realpath(__DIR__ . '/src/config.php'));
} catch (Exception $e) {
    echo '<p>' . $e->getMessage() . '</p>';
}
