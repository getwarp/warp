<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\Exception\FriendlyExceptionTrait;
use spaceonfire\Exception\MessageTemplate;
use spaceonfire\Exception\TranslatableExceptionTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Webmozart\Expression\Expression;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class ExpressionNotSupportedException extends \InvalidArgumentException implements
    TranslatableInterface,
    FriendlyExceptionInterface
{
    use TranslatableExceptionTrait;
    use FriendlyExceptionTrait;

    /**
     * @param MessageTemplate|scalar|\Stringable $message
     * @noinspection MagicMethodsValidityInspection PhpMissingParentConstructorInspection
     */
    private function __construct($message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->construct($message, $code, $previous);
    }

    public static function new(Expression $expression): self
    {
        return new self(\sprintf('Not supported expression class: %s.', \get_class($expression)));
    }

    public static function cannotBeNegated(Expression $expression): self
    {
        return new self(\sprintf('Cannot negate expression: %s.', \get_class($expression)));
    }

    protected static function getDefaultName(): string
    {
        return 'Expression not supported';
    }
}
