<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

interface FieldFactoryInterface
{
    public function enabled(): bool;

    public function make(string $field): FieldInterface;
}
