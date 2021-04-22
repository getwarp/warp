<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use Spiral\Database\DatabaseInterface;

final class CsvFileFixtureLoader
{
    private DatabaseInterface $database;
    private string $directory;
    private string $separator;
    private string $enclosure;
    private string $escape;

    public function __construct(
        DatabaseInterface $database,
        string $directory = '',
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ) {
        $this->database = $database;
        $this->directory = $directory;
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function load(string $filename, ?string $table = null): void
    {
        $filename = $this->prepareFilePath($filename);
        $file = new \SplFileObject($filename, 'r+b');
        $table ??= $file->getBasename('.' . $file->getExtension());

        $insert = $this->database->insert($table);

        $columns = $this->readRowFrom($file);

        if ([] === $columns || null === $columns) {
            throw new \RuntimeException(\sprintf('Empty fixture file given: %s', $filename));
        }

        $insert->columns($columns);

        while (null !== $row = $this->readRowFrom($file)) {
            if ([] === $row) {
                continue;
            }

            $insert->values($row);
        }

        $insert->run();
    }

    private function readRowFrom(\SplFileObject $file): ?array
    {
        $data = $file->fgetcsv($this->separator, $this->enclosure, $this->escape);

        if (!is_array($data)) {
            return null;
        }

        // Empty rows
        if (1 === count($data) && null === $data[0]) {
            return [];
        }

        return $data;
    }

    private function prepareFilePath(string $filename): string
    {
        return $this->directory . '/' . \ltrim($filename, '/');
    }
}
