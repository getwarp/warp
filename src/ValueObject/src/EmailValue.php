<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use Webmozart\Assert\Assert;

class EmailValue extends StringValue
{
    public function __construct(string $value)
    {
        Assert::email($value);
        parent::__construct($value);
    }
}
