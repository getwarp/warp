<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Exception;

use spaceonfire\Exception\TranslatableExceptionTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class MigrationLockedException extends \RuntimeException implements
    TranslatableInterface,
    FriendlyExceptionInterface
{
    use TranslatableExceptionTrait;

    /**
     * @noinspection MagicMethodsValidityInspection PhpMissingParentConstructorInspection
     */
    public function __construct(?\Throwable $previous = null)
    {
        $this->construct(
            <<<TEXT
            {$this->getName()}

            {$this->getSolution()}
            TEXT,
            0,
            $previous,
        );
    }

    public function getName(): string
    {
        return 'Migrator operations locked by other process';
    }

    public function getSolution(): ?string
    {
        return 'Wait for other migration process finish.';
    }
}
