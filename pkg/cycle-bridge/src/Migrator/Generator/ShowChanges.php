<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Generator;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Symfony\Component\Console\Style\OutputStyle;

final class ShowChanges implements GeneratorInterface
{
    private const ADDITION = 1;

    private const DROP = 0;

    private OutputStyle $out;

    /**
     * @var array<string,array{database:string,schema:AbstractTable}>
     */
    private array $changes = [];

    public function __construct(OutputStyle $style)
    {
        $this->out = $style;
    }

    public function run(Registry $registry): Registry
    {
        $this->out->title('Detecting schema changes:');

        $this->changes = [];
        foreach ($registry->getIterator() as $e) {
            if (!$registry->hasTable($e)) {
                continue;
            }

            $table = $registry->getTableSchema($e);
            if (!$table->getComparator()->hasChanges()) {
                continue;
            }

            $database = $registry->getDatabase($e);
            $this->changes[$database . ':' . $table->getName()] = [
                'database' => $database,
                'schema' => $table,
            ];
        }

        if ([] === $this->changes) {
            $this->out->text('No database changes has been detected');

            return $registry;
        }

        foreach ($this->changes as $change) {
            $this->describeChanges($change['database'], $change['schema']);
        }

        if (!$this->out->isVerbose()) {
            $this->out->note('Use verbose output (-v) to see changes details.');
        }

        return $registry;
    }

    public function hasChanges(): bool
    {
        return [] !== $this->changes;
    }

    private function describeChanges(string $database, AbstractTable $table): void
    {
        if (!$this->out->isVerbose()) {
            $this->out->text(\sprintf(
                '%s.%s: <fg=green>%s</> change(s) detected',
                $database,
                $table->getName(),
                $this->numChanges($table),
            ));

            return;
        }

        $this->out->section(\sprintf('%s.%s', $database, $table->getName()));

        if (!$table->exists()) {
            $this->out->text($this->formatChange('create table', self::ADDITION));
        }

        if (AbstractTable::STATUS_DECLARED_DROPPED === $table->getStatus()) {
            $this->out->text($this->formatChange('drop table', self::DROP));
            return;
        }

        $cmp = $table->getComparator();

        // Columns
        foreach ($cmp->addedColumns() as $column) {
            $this->out->text($this->formatChange(\sprintf('add column %s', $column->getName()), self::ADDITION));
        }

        foreach ($cmp->droppedColumns() as $column) {
            $this->out->text($this->formatChange(\sprintf('drop column %s', $column->getName()), self::DROP));
        }

        foreach ($cmp->alteredColumns() as $column) {
            $column = $column[0];
            $this->out->text($this->formatChange(\sprintf('alter column %s', $column->getName()), self::ADDITION));
        }

        // Indexes
        foreach ($cmp->addedIndexes() as $index) {
            $this->out->text($this->formatChange(
                \sprintf('add index on [%s]', \implode(', ', $index->getColumns())),
                self::ADDITION
            ));
        }

        foreach ($cmp->droppedIndexes() as $index) {
            $this->out->text($this->formatChange(
                \sprintf('drop index on [%s]', \implode(', ', $index->getColumns())),
                self::DROP
            ));
        }

        foreach ($cmp->alteredIndexes() as $index) {
            $index = $index[0];
            $this->out->text($this->formatChange(
                \sprintf('alter index on [%s]', \implode(', ', $index->getColumns())),
                self::ADDITION
            ));
        }

        // Foreign keys
        foreach ($cmp->addedForeignKeys() as $fk) {
            $this->out->text($this->formatChange(
                \sprintf('add foreign key on %s', \implode(', ', $fk->getColumns())),
                self::ADDITION
            ));
        }

        foreach ($cmp->droppedForeignKeys() as $fk) {
            $this->out->text($this->formatChange(
                \sprintf('drop foreign key on %s', \implode(', ', $fk->getColumns())),
                self::DROP
            ));
        }

        foreach ($cmp->alteredForeignKeys() as $fk) {
            $fk = $fk[0];
            $this->out->text($this->formatChange(
                \sprintf('alter foreign key %s', \implode(', ', $fk->getColumns())),
                self::ADDITION
            ));
        }
    }

    private function formatChange(string $message, int $type): string
    {
        if (self::ADDITION === $type) {
            return \sprintf('<fg=green>+ %s</>', $message);
        }

        if (self::DROP === $type) {
            return \sprintf('<fg=red>- %s</>', $message);
        }

        return $message;
    }

    private function numChanges(AbstractTable $table): int
    {
        $cmp = $table->getComparator();

        return \count($cmp->addedColumns())
            + \count($cmp->droppedColumns())
            + \count($cmp->alteredColumns())
            + \count($cmp->addedIndexes())
            + \count($cmp->droppedIndexes())
            + \count($cmp->alteredIndexes())
            + \count($cmp->addedForeignKeys())
            + \count($cmp->droppedForeignKeys())
            + \count($cmp->alteredForeignKeys());
    }
}
