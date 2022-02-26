<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator;

use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorDownCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorFreshCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorGenerateCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorListCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorMakeCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorReplayCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorSyncCommand;
use spaceonfire\Bridge\Cycle\Migrator\Command\MigratorUpCommand;
use spaceonfire\Container\DefinitionInterface;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\Factory\DefinitionTag;
use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\Console\Command\Command;

abstract class AbstractMigratorProvider extends AbstractServiceProvider
{
    public const COMMANDS = [
        MigratorListCommand::class,
        MigratorUpCommand::class,
        MigratorDownCommand::class,
        MigratorReplayCommand::class,
        MigratorMakeCommand::class,
        MigratorFreshCommand::class,
        MigratorGenerateCommand::class,
        MigratorSyncCommand::class,
    ];

    public function provides(): iterable
    {
        if (!$this->getContainer()->has(DatabaseManager::class)) {
            throw NotFoundException::alias(DatabaseManager::class);
        }

        yield MigrationConfig::class;
        yield Migrator::class;

        yield DefinitionTag::CONSOLE_COMMAND;

        foreach (static::COMMANDS as $command) {
            if ($this->isCommandAvailable($command)) {
                yield $command;
            }
        }
    }

    public function register(): void
    {
        $this->getContainer()->define(MigrationConfig::class, [$this, 'makeMigrationConfig'], true);
        $this->getContainer()->define(Migrator::class, [$this, 'makeMigrator'], true);

        foreach (static::COMMANDS as $command) {
            $this->registerCommand($command);
        }
    }

    /**
     * @return MigrationConfig<string,mixed>
     */
    abstract public function makeMigrationConfig(): MigrationConfig;

    public function makeMigrator(): Migrator
    {
        $config = $this->getContainer()->get(MigrationConfig::class);
        $dbal = $this->getContainer()->get(DatabaseManager::class);
        $migrator = new Migrator($config, $dbal, new FileRepository($config));
        $migrator->configure();
        return $migrator;
    }

    protected function isCommandAvailable(string $class): bool
    {
        if (MigratorGenerateCommand::class === $class) {
            return \class_exists(GenerateMigrations::class) && $this->getContainer()->has(Registry::class);
        }

        if (MigratorSyncCommand::class === $class) {
            return $this->getContainer()->has(Registry::class);
        }

        return true;
    }

    /**
     * @param DefinitionInterface<Command> $definition
     */
    protected function extendCommand(DefinitionInterface $definition): void
    {
    }

    /**
     * @param class-string<Command> $command
     */
    private function registerCommand(string $command): void
    {
        if (!$this->isCommandAvailable($command)) {
            return;
        }

        /** @phpstan-var DefinitionInterface<Command> $def */
        $def = $this->getContainer()->define($command)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->extendCommand($def);
    }
}
