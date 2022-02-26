<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Command;

use Psr\Container\ContainerInterface;
use spaceonfire\Bridge\Cycle\Migrator\Handler;
use spaceonfire\Bridge\Cycle\Migrator\Input\InputArgument;
use spaceonfire\Bridge\Cycle\Migrator\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigratorMakeCommand extends Command
{
    protected static $defaultName = 'migrator:make';

    protected static $defaultDescription = 'Create a new migration file';

    /**
     * @var InputArgument<string>
     */
    private InputArgument $name;

    /**
     * @var InputOption<string>
     */
    private InputOption $database;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, ?string $name = null)
    {
        $this->container = $container;

        parent::__construct($name);

        $this->name = new InputArgument('name', InputArgument::REQUIRED, 'A migration filename');
        $this->name->register($this);

        $this->database = new InputOption('database', null, InputOption::VALUE_OPTIONAL, 'A database name', 'default');
        $this->database->register($this);
    }

    public function getMigrationName(InputInterface $input): string
    {
        return $this->name->getValueFrom($input);
    }

    public function getMigrationDatabase(InputInterface $input): string
    {
        return $this->database->getValueFrom($input);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        return $this->container->get(Handler\MigratorMakeCommandHandler::class)->handle($this, $input, $io);
    }
}
