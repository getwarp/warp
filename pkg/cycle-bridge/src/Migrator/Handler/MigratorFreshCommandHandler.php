<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Handler;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Table;
use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\Migrator;
use spaceonfire\Collection\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class MigratorFreshCommandHandler extends AbstractCommandHandler
{
    private Migrator $migrator;

    private DatabaseProviderInterface $dbal;

    public function __construct(Migrator $migrator, DatabaseProviderInterface $dbal)
    {
        $this->migrator = $migrator;
        $this->dbal = $dbal;
    }

    public function handle(Command $command, InputInterface $input, OutputStyle $style): int
    {
        $databases = Collection::new($this->migrator->getMigrations())
            ->map(static fn (MigrationInterface $migration) => $migration->getDatabase())
            ->unique()
            ->map(fn (?string $db) => $this->dbal->database($db))
            ->all();

        if (0 === \count($databases)) {
            $databases = [$this->dbal->database()];
        }

        foreach ($databases as $database) {
            $database->transaction(static function (DatabaseInterface $database) use ($style): void {
                /** @var array<Table<mixed>> $tables */
                $tables = $database->getTables();

                foreach ($tables as $table) {
                    $tableSchema = $table->getSchema();

                    foreach ($tableSchema->getForeignKeys() as $foreignKey) {
                        $tableSchema->dropForeignKey($foreignKey->getColumns());
                        $style->text(\sprintf('Foreign key "%s" dropped', $foreignKey->getName()));
                    }

                    $tableSchema->save();
                }

                foreach ($tables as $table) {
                    $tableSchema = $table->getSchema();
                    $tableSchema->declareDropped();
                    $tableSchema->save();
                    $style->writeln(\sprintf('Table "%s" dropped', $table->getName()));
                }
            });
        }

        $this->migrator->configure();

        return Command::SUCCESS;
    }
}
