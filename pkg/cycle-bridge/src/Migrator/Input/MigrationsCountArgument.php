<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Input;

/**
 * @extends InputArgument<int|null>
 */
final class MigrationsCountArgument extends InputArgument
{
    public function __construct(
        string $name = 'count',
        string $description = 'Count of migrations to process',
        ?int $default = null
    ) {
        parent::__construct($name, self::OPTIONAL, $description, $default);
    }
}
