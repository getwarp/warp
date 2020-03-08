<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use Webmozart\Assert\Assert;

class IpValue extends StringValue
{
    public function __construct(string $value)
    {
        Assert::ip($value);
        parent::__construct($value);
    }
}
