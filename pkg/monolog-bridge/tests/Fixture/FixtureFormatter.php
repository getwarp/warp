<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Fixture;

use Monolog\Formatter\FormatterInterface;

final class FixtureFormatter implements FormatterInterface
{
    public function format(array $record): string
    {
        return $record['message'];
    }

    public function formatBatch(array $records): array
    {
        return \array_map([$this, 'format'], $records);
    }
}
