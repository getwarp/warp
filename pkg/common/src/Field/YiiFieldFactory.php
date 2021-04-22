<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

use spaceonfire\Exception\PackageMissingException;
use Yiisoft\Arrays\ArrayHelper;

final class YiiFieldFactory implements FieldFactoryInterface
{
    public function enabled(): bool
    {
        return \class_exists(ArrayHelper::class);
    }

    public function make(string $field): YiiField
    {
        if (!$this->enabled()) {
            throw PackageMissingException::new('yiisoft/arrays', null, self::class);
        }

        return new YiiField($field);
    }
}
