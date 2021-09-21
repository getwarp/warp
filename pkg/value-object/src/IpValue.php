<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Throwable;
use Webmozart\Assert\Assert;

class IpValue extends StringValue
{
    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        $isValid = parent::validate($value);

        if ($isValid) {
            try {
                Assert::ip($value);
                return true;
            } catch (Throwable $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if (null !== $value) {
            throw new InvalidArgumentException(sprintf('Expected a value to be an IP. Got "%s"', $value));
        }

        parent::throwExceptionForInvalidValue($value);
    }
}
