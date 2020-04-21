<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class UriValue extends BaseValueObject
{
    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        return (is_string($value) && parse_url($value) !== false) || $value instanceof UriInterface;
    }

    /**
     * @inheritDoc
     * @return UriInterface
     */
    protected function cast($value): UriInterface
    {
        return !$value instanceof UriInterface ? new Uri($value) : $value;
    }

    /**
     * @inheritDoc
     */
    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException(sprintf('Expected a value to be a valid uri. Got "%s"', $value));
        }

        parent::throwExceptionForInvalidValue($value);
    } // @codeCoverageIgnore

    /**
     * @inheritDoc
     * @return UriInterface
     */
    public function value(): UriInterface
    {
        return parent::value();
    }
}
