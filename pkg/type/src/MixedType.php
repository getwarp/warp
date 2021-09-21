<?php

declare(strict_types=1);

namespace spaceonfire\Type;

final class MixedType implements TypeInterface
{
    public const NAME = 'mixed';

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function check($_): bool
    {
        return true;
    }
}
