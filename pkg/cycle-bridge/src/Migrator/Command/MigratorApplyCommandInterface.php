<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Command;

use Symfony\Component\Console\Input\InputInterface;

interface MigratorApplyCommandInterface
{
    /**
     * @return int<0,max>
     */
    public function getMigrationsCount(InputInterface $input): int;
}
