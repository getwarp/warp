<?php

declare(strict_types=1);

use Cycle\Database\Driver\SQLite\SQLiteDriver;

return [
    'default' => 'default',
    'databases' => [
        'default' => [
            'connection' => 'sqlite',
        ],
    ],
    'connections' => [
        'sqlite' => [
            'driver' => SQLiteDriver::class,
            'options' => [
                'connection' => 'sqlite::memory:',
                'username' => '',
                'password' => '',
            ],
        ],
    ],
];
