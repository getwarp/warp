<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Command;

use Psr\Container\ContainerInterface;
use spaceonfire\Bridge\Cycle\Migrator\Handler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigratorListCommand extends Command
{
    protected static $defaultName = 'migrator:list';

    protected static $defaultDescription = 'List all available migrations with their statuses';

    private string $dateFormat;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, string $dateFormat = 'r', ?string $name = null)
    {
        $this->container = $container;
        $this->dateFormat = $dateFormat;

        parent::__construct($name);
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        return $this->container->get(Handler\MigratorListCommandHandler::class)->handle($this, $input, $io);
    }
}
