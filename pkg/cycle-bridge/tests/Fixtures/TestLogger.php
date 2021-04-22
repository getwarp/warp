<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

final class TestLogger implements LoggerInterface
{
    use LoggerTrait;

    private bool $display = false;

    private int $countWrites = 0;

    private int $countReads = 0;

    public function countWriteQueries(): int
    {
        return $this->countWrites;
    }

    public function countReadQueries(): int
    {
        return $this->countReads;
    }

    public function log($level, $message, array $context = []): void
    {
        if (!empty($context['elapsed'])) {
            $sql = \strtolower($message);
            if (
                \str_starts_with($sql, 'insert') ||
                \str_starts_with($sql, 'update') ||
                \str_starts_with($sql, 'delete')
            ) {
                $this->countWrites++;
            } elseif (!$this->isPostgresSystemQuery($sql)) {
                $this->countReads++;
            }
        }

        if (!$this->display) {
            return;
        }

        if ($level === LogLevel::ERROR) {
            echo " \n! \033[31m" . $message . "\033[0m";
        } elseif ($level === LogLevel::ALERT) {
            echo " \n! \033[35m" . $message . "\033[0m";
        } elseif (\str_starts_with($message, 'SHOW')) {
            echo " \n> \033[34m" . $message . "\033[0m";
        } else {
            if ($this->isPostgresSystemQuery($message)) {
                echo " \n> \033[90m" . $message . "\033[0m";

                return;
            }

            if (\str_starts_with($message, 'SELECT')) {
                echo " \n> \033[32m" . $message . "\033[0m";
            } elseif (strpos($message, 'INSERT') === 0) {
                echo " \n> \033[36m" . $message . "\033[0m";
            } else {
                echo " \n> \033[33m" . $message . "\033[0m";
            }
        }
    }

    public function display(): void
    {
        $this->display = true;
    }

    public function hide(): void
    {
        $this->display = false;
    }

    protected function isPostgresSystemQuery(string $query): bool
    {
        $query = \strtolower($query);

        return \str_contains($query, 'tc.constraint_name') ||
            \str_contains($query, 'pg_indexes') ||
            \str_contains($query, 'tc.constraint_name') ||
            \str_contains($query, 'pg_constraint') ||
            \str_contains($query, 'information_schema') ||
            \str_contains($query, 'pg_class');
    }
}
