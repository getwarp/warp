<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Cycle\Migrations\Migrator;
use Cycle\Schema\Generator\Migrations\MigrationImage;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorMakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class MigratorMakeCommandHandler extends AbstractCommandHandler
{
    private Migrator $migrator;

    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    public function handle(Command $command, InputInterface $input, OutputStyle $style): int
    {
        if (!$command instanceof MigratorMakeCommand) {
            return Command::INVALID;
        }

        $image = new MigrationImage($this->migrator->getConfig(), $command->getMigrationDatabase($input));
        $image->setName($command->getMigrationName($input));

        $file = $this->migrator->getRepository()->registerMigration(
            $image->buildFileName(),
            $image->getClass()->getName(),
            $image->getFile()->render()
        );

        $style->success(\sprintf('Created Migration: %s', $file));

        return Command::SUCCESS;
    }
}
