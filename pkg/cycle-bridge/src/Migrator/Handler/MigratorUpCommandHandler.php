<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Cycle\Migrations\Migrator;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorApplyCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class MigratorUpCommandHandler extends AbstractCommandHandler
{
    private Migrator $migrator;

    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    public function handle(Command $command, InputInterface $input, OutputStyle $style): int
    {
        if (!$command instanceof MigratorApplyCommandInterface) {
            return Command::INVALID;
        }

        $count = $command->getMigrationsCount($input);

        $applied = 0;

        do {
            $duration = \microtime(true);
            $migration = $this->migrator->run();
            $duration = \microtime(true) - $duration;

            if (null === $migration) {
                break;
            }

            $style->text(\sprintf(
                'Migrated: %s (%s seconds)',
                $migration->getState()->getName(),
                \number_format($duration, 2),
            ));
            $applied++;
        } while (0 === $count || $applied < $count);

        if (0 === $applied) {
            $style->text('Nothing to migrate.');
        }

        return Command::SUCCESS;
    }
}
