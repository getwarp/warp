<?php

ini_set('error_reporting', E_ALL);

define('SOF_ENV_PATH', __DIR__ . '/pkg/common/tests/_Fixtures');
define('SOF_ENV_FILE_NAME', 'test.env');

$findAutoload = static function (): string {
    $paths = [
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/../../vendor/autoload.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    throw new RuntimeException('Composer autoloader not found. Make sure you ran "composer install"');
};

$autoload = $findAutoload();
$vendorDir = dirname($autoload);

require_once $autoload;
