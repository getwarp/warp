<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

abstract class AbstractCommandHandler
{
    abstract public function handle(Command $command, InputInterface $input, OutputStyle $style): int;
}
