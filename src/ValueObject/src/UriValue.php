<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Webmozart\Assert\Assert;

class UriValue extends BaseValueObject
{
    /**
     * UriValueObject constructor.
     * @param UriInterface|string $value
     */
    public function __construct($value)
    {
        if (is_string($value)) {
            $value = new Uri($value);
        }

        Assert::isInstanceOf($value, UriInterface::class);

        parent::__construct($value);
    }

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
