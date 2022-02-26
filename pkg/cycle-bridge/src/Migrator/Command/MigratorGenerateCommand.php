<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Command;

use Psr\Container\ContainerInterface;
use spaceonfire\Bridge\Cycle\Migrator\Handler;
use spaceonfire\Bridge\Cycle\Migrator\Input;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigratorGenerateCommand extends Command
{
    protected static $defaultName = 'migrator:generate';

    protected static $defaultDescription = 'Generate migrations from Cycle ORM schema';

    private Input\DryRunOption $dryRun;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, ?string $name = null)
    {
        $this->container = $container;

        parent::__construct($name);

        $this->dryRun = new Input\DryRunOption();
        $this->dryRun->register($this);
    }

    public function isDryRun(InputInterface $input): bool
    {
        return $this->dryRun->getValueFrom($input);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        return $this->container->get(Handler\MigratorGenerateCommandHandler::class)->handle($this, $input, $io);
    }
}
