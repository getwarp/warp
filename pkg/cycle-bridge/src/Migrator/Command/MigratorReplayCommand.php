<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Command;

use Psr\Container\ContainerInterface;
use spaceonfire\Bridge\Cycle\Migrator\Handler;
use spaceonfire\Bridge\Cycle\Migrator\Input;
use spaceonfire\Bridge\Cycle\Migrator\LockFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigratorReplayCommand extends Command implements MigratorApplyCommandInterface
{
    protected static $defaultName = 'migrator:replay';

    protected static $defaultDescription = 'Rollback then run migrations';

    private Input\ForceOption $force;

    private Input\MigrationsCountArgument $count;

    private ContainerInterface $container;

    private LockFacade $lockFacade;

    public function __construct(ContainerInterface $container, ?string $name = null)
    {
        $this->container = $container;

        $this->lockFacade = new LockFacade($this->container);

        parent::__construct($name);

        $this->force = new Input\ForceOption();
        $this->force->configure($container);
        $this->force->register($this);

        $this->count = new Input\MigrationsCountArgument();
        $this->count->register($this);
    }

    public function getMigrationsCount(InputInterface $input): int
    {
        return (int)\max($this->count->getValueFrom($input) ?? 0, 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->force->confirm($input, $io);

        $this->lockFacade->acquire();

        try {
            $exitCode = $this->container->get(Handler\MigratorDownCommandHandler::class)->handle($this, $input, $io);
            if (self::SUCCESS !== $exitCode) {
                return $exitCode;
            }

            return $this->container->get(Handler\MigratorUpCommandHandler::class)->handle($this, $input, $io);
        } finally {
            $this->lockFacade->release();
        }
    }
}
