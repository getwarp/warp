<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Symfony\Contracts\Translation\TranslatableInterface;
use Warp\Exception\FriendlyExceptionTrait;
use Warp\Exception\MessageTemplate;
use Warp\Exception\TranslatableExceptionTrait;
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
