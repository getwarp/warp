<?php

/**
 * @phpcs:disable PSR12.Classes.ClosingBrace.StatementAfter
 */

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class UriValue extends BaseValueObject
{
    protected function validate($value): bool
    {
        return (is_string($value) && parse_url($value) !== false) || $value instanceof UriInterface;
    }

    protected function cast($value)
    {
        return !$value instanceof UriInterface ? new Uri($value) : $value;
    }

    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException(
                sprintf('Expected a value to be a valid uri. Got "%s"', $value)
            );
        }

        parent::throwExceptionForInvalidValue($value);
    } // @codeCoverageIgnore

    /**
     * @inheritDoc
     */
    public function value(): UriInterface
    {
        return parent::value();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return (string)$this->value();
    }
}
