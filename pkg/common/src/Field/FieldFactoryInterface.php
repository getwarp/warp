<?php

declare(strict_types=1);

namespace Warp\Common\Field;

interface FieldFactoryInterface
{
    public function enabled(): bool;

    public function make(string $field): FieldInterface;
}
