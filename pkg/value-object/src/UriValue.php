<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriInterface;

class UriValue extends AbstractValueObject
{
    public function value(): UriInterface
    {
        return parent::value();
    }

    public function jsonSerialize(): string
    {
        return (string)$this->value;
    }

    protected static function validate($value): void
    {
        if ($value instanceof UriInterface) {
            return;
        }

        if (
            (\is_string($value) || (\is_object($value) && \method_exists($value, '__toString')))
            && false !== \parse_url((string)$value)
        ) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            '%s expected a value to be a valid uri. Got: %s.',
            static::class,
            self::checkValueType($value) ? $value : \get_debug_type($value)
        ));
    }

    protected static function cast($value): UriInterface
    {
        return $value instanceof UriInterface
            ? $value
            : Psr17FactoryDiscovery::findUriFactory()->createUri((string)$value);
    }
}
