<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\Exception\ClientFriendlyExceptionInterface;
use spaceonfire\Exception\FriendlyExceptionTrait;
use spaceonfire\Exception\MessageTemplate;
use spaceonfire\Exception\TranslatableExceptionTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
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
