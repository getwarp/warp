<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Cycle\Migrations\Migrator;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorGenerateCommand;
use spaceonfire\Bridge\Cycle\Migrator\Generator\ShowChanges;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class MigratorGenerateCommandHandler extends AbstractCommandHandler
{
    private Registry $registry;

    private Migrator $migrator;

    public function __construct(Registry $registry, Migrator $migrator)
    {
        $this->registry = $registry;
        $this->migrator = $migrator;
    }

    public function handle(Command $command, InputInterface $input, OutputStyle $style): int
    {
        if (!$command instanceof MigratorGenerateCommand) {
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

        $generator = new GenerateMigrations($this->migrator->getRepository(), $this->migrator->getConfig());

        $generator->run($this->registry);

        return Command::SUCCESS;
    }
}
