<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Symfony\Contracts\Translation\TranslatableInterface;
use Warp\Exception\ClientFriendlyExceptionInterface;
use Warp\Exception\FriendlyExceptionTrait;
use Warp\Exception\MessageTemplate;
use Warp\Exception\TranslatableExceptionTrait;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class EntityNotFoundException extends \OutOfBoundsException implements
    TranslatableInterface,
    FriendlyExceptionInterface,
    ClientFriendlyExceptionInterface
{
    use TranslatableExceptionTrait;
    use FriendlyExceptionTrait;

    /**
     * @param MessageTemplate|scalar|\Stringable $message
     * @noinspection MagicMethodsValidityInspection PhpMissingParentConstructorInspection
     */
    public function __construct($message = 'Entity not found', int $code = 0, ?\Throwable $previous = null)
    {
        $this->construct($message, $code, $previous);
    }

    /**
     * @param string $entity
     * @param scalar|\Stringable $primary
     * @return self
     */
    public static function byPrimary(string $entity, $primary): self
    {
        return new self(MessageTemplate::new('Entity "%entity%" not found by primary: %primary%.', [
            '%entity%' => $entity,
            '%primary%' => $primary,
        ]));
    }

    protected static function getDefaultName(): string
    {
        return 'Entity not found';
    }
}
