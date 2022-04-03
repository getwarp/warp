<?php

declare(strict_types=1);

namespace Warp\ValueObject;

use InvalidArgumentException;
use Throwable;
use Webmozart\Assert\Assert;

class EmailValue extends StringValue
{
    /**
     * @inheritDoc
     */
    protected function validate($value): bool
    {
        $isValid = parent::validate($value);

        if ($isValid) {
            try {
                Assert::email($value);
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
            throw new InvalidArgumentException(
                sprintf('Expected a value to be a valid e-mail address. Got "%s"', $value)
            );
        }

        parent::throwExceptionForInvalidValue($value);
    }
}
