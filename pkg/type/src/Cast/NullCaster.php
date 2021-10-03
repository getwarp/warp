<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

final class NullCaster implements CasterInterface
{
    public const ACCEPT_NULL = '@null';

    public const ACCEPT_EMPTY = '@empty';

    public const ACCEPT_ALL = '@all';

    private string $accept;

    /**
     * @phpstan-param self::ACCEPT_* $accept
     */
    public function __construct(string $accept = self::ACCEPT_ALL)
    {
        $this->accept = $accept;
    }

    public function accepts($value): bool
    {
        if (self::ACCEPT_ALL === $this->accept) {
            return true;
        }

        if (self::ACCEPT_EMPTY === $this->accept) {
            return empty($value);
        }

        return null === $value;
    }

    public function cast($value)
    {
        if (!$this->accepts($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Given value (%s) cannot be casted to null.',
                \get_debug_type($value),
            ));
        }

        return null;
    }
}
