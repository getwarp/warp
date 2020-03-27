<?php

/**
 * @phpcs:disable PSR12.Classes.ClosingBrace.StatementAfter
 */

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use InvalidArgumentException;
use Throwable;
use Webmozart\Assert\Assert;

class IpValue extends StringValue
{
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

    protected function throwExceptionForInvalidValue(?string $value): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException(
                sprintf('Expected a value to be an IP. Got "%s"', $value)
            );
        }

        parent::throwExceptionForInvalidValue($value);
    } // @codeCoverageIgnore
}
