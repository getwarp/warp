<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Migrator;

use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use Spiral\Core\FactoryInterface as SpiralFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Warp\Bridge\Cycle\Factory\SpiralFactory;
use Warp\Bridge\Cycle\Migrator\Command\MigratorDownCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorFreshCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorGenerateCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorListCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorMakeCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorReplayCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorSyncCommand;
use Warp\Bridge\Cycle\Migrator\Command\MigratorUpCommand;
use Warp\Container\DefinitionInterface;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Factory\DefinitionTag;
use Warp\Container\Factory\Reflection\ReflectionFactoryAggregate;
use Warp\Container\FactoryAggregateInterface;
use Warp\Container\InstanceOfAliasContainer;
use Warp\Container\ServiceProvider\AbstractServiceProvider;

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
        $container = InstanceOfAliasContainer::wrap($this->getContainer());

        $config = $container->get(MigrationConfig::class);
        $dbal = $container->get(DatabaseManager::class);

        if ($container->has(SpiralFactoryInterface::class)) {
            $factory = $container->get(SpiralFactoryInterface::class);
        } else {
            $factory = new SpiralFactory(
                $container->has(FactoryAggregateInterface::class)
                    ? $container->get(FactoryAggregateInterface::class)
                    : new ReflectionFactoryAggregate($container)
            );
        }

        $migrator = new Migrator($config, $dbal, new FileRepository($config, $factory));
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
