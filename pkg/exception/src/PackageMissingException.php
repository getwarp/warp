<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use Symfony\Contracts\Translation\TranslatableInterface;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class PackageMissingException extends \RuntimeException implements
    TranslatableInterface,
    FriendlyExceptionInterface
{
    use TranslatableExceptionTrait;
    use FriendlyExceptionTrait;

    /**
     * @param MessageTemplate|scalar|\Stringable $message
     * @param MessageTemplate|scalar|\Stringable|null $solution
     * @noinspection MagicMethodsValidityInspection PhpMissingParentConstructorInspection
     */
    private function __construct($message, $solution = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->construct($message, $code, $previous);

        $this->solution = null === $solution ? null : MessageTemplate::wrap($solution);
    }

    public static function new(string $package, ?string $version = null, ?string $scope = null): self
    {
        $message = MessageTemplate::new(
            null === $scope
                ? 'Package "%package%" is not installed.'
                : '%scope% requires package "%package%", which seems is not installed.',
            [
                '%package%' => $package,
                '%scope%' => $scope ?? '',
            ]
        );

        $solution = MessageTemplate::new('Run "composer require %package%%version%" in command line.', [
            '%package%' => $package,
            '%version%' => null === $version ? '' : ':' . $version,
        ]);

        return new self($message, $solution);
    }

    protected static function getDefaultName(): string
    {
        return 'Package missing';
    }
}
