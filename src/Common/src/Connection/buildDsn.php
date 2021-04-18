<?php

declare(strict_types=1);

namespace spaceonfire\Common\Connection;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Composes Data Source Name (DSN) from given options
 * @see https://www.php.net/manual/ru/ref.pdo-mysql.connection.php
 * @see https://www.php.net/manual/ru/ref.pdo-pgsql.connection.php
 * @see https://www.php.net/manual/ru/ref.pdo-sqlsrv.connection.php
 * @see https://www.php.net/manual/ru/ref.pdo-oci.connection.php
 * @see https://www.php.net/manual/ru/ref.pdo-sqlite.connection.php
 * @param array<string,string> $options DSN elements
 * @param string $driver DSN driver prefix (`mysql` by default).
 * @return string
 */
function buildDsn(array $options, string $driver = 'mysql'): string
{
    if (in_array($driver, ['sqlite', 'sqlite2'], true)) {
        Assert::count($options, 1);
        return $driver . ':' . array_values($options)[0];
    }

    $optionsOrderMap = [
        'mysql' => [
            'host',
            'port',
            'dbname',
            'unix_socket',
            'charset',
        ],
        'pgsql' => [
            'host',
            'port',
            'dbname',
            'user',
            'password',
        ],
        'sqlsrv' => [
            'Server',
            'Database',
            'APP',
            'ConnectionPooling',
            'Encrypt',
            'Failover_Partner',
            'LoginTimeout',
            'MultipleActiveResultSets',
            'QuotedId',
            'TraceFile',
            'TraceOn',
            'TransactionIsolation',
            'TrustServerCertificate',
            'WSID',
        ],
        'oci' => [
            'dbname',
            'charset',
        ],
    ];

    if (!isset($optionsOrderMap[$driver])) {
        throw new InvalidArgumentException('Unknown DSN driver: ' . $driver);
    }

    $optionsByOrder = $optionsOrderMap[$driver];

    $optionsFiltered = [];

    foreach ($optionsByOrder as $optionName) {
        if (!isset($options[$optionName]) || empty($options[$optionName])) {
            continue;
        }

        $optionsFiltered[$optionName] = $options[$optionName];
    }

    $optionsString = implode(';', array_map(
        static function ($v, $k) {
            return sprintf('%s=%s', $k, $v);
        },
        array_values($optionsFiltered),
        array_keys($optionsFiltered)
    ));

    return $driver . ':' . $optionsString;
}
