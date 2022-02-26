<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorSyncCommand;
use spaceonfire\Bridge\Cycle\Migrator\Generator\ShowChanges;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class MigratorSyncCommandHandler extends AbstractCommandHandler
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function handle(Command $command, InputInterface $input, OutputStyle $style): int
    {
        if (!$command instanceof MigratorSyncCommand) {
            return Command::INVALID;
        }

        $show = new ShowChanges($style);
        $show->run($this->registry);
        if (!$show->hasChanges()) {
            return Command::SUCCESS;
        }

        if ($command->isDryRun($input)) {
            return Command::SUCCESS;
        }

        $generator = new SyncTables();
        $generator->run($this->registry);

        $style->success('ORM Schema has been synchronized!');

        return Command::SUCCESS;
    }
}
