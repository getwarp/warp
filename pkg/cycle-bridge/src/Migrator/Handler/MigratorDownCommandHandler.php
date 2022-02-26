<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Cycle\Migrations\Migrator;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorApplyCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class MigratorDownCommandHandler extends AbstractCommandHandler
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

        $reverted = 0;

        do {
            $duration = \microtime(true);
            $migration = $this->migrator->rollback();
            $duration = \microtime(true) - $duration;

            if (null === $migration) {
                break;
            }

            $style->text(\sprintf(
                'Reverted: %s (%s seconds)',
                $migration->getState()->getName(),
                \number_format($duration, 2),
            ));
            $reverted++;
        } while (0 === $count || $reverted < $count);

        if (0 === $reverted) {
            $style->text('Nothing to rollback.');
        }

        return Command::SUCCESS;
    }
}
