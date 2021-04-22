<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

final class DefaultFieldFactory implements FieldFactoryInterface
{
    public function enabled(): bool
    {
        return true;
    }

    public function make(string $field): DefaultField
    {
        return new DefaultField($field);
    }
}
