<?php

ini_set('error_reporting', E_ALL);

$findAutoload = static function (): string {
    $paths = [
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../../../vendor/autoload.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    throw new RuntimeException('Composer autoloader not found. Make sure you ran "composer install"');
};

$autoload = $findAutoload();

require_once $autoload;
