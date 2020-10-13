<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use RuntimeException;

final class VoidType implements Type
{
    public const NAME = 'void';

    /**
     * @inheritDoc
     */
    public function check($_): bool
    {
        throw new RuntimeException('Void type cannot be checked');
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return self::NAME;
    }
}
