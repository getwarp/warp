<?php

declare(strict_types=1);

namespace Warp\Common\Field;

use Warp\Exception\PackageMissingException;
use Yiisoft\Arrays\ArrayHelper;

final class YiiField implements FieldInterface
{
    private string $field;

    /**
     * @var string[]
     */
    private array $elements;

    /**
     * @var array-key|array<array-key>|\Closure
     */
    private $extractKey;

    /**
     * @param string $field
     * @param array-key|array<array-key>|\Closure|null $extractKey
     */
    public function __construct(string $field, $extractKey = null)
    {
        if (!\class_exists(ArrayHelper::class)) {
            throw PackageMissingException::new('yiisoft/arrays', null, self::class);
        }

        $this->field = $field;
        $this->elements = DefaultField::parseElements($field);
        $this->extractKey = $extractKey ?? $this->elements;
    }

    public function __toString(): string
    {
        return $this->field;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function extract($element)
    {
        return ArrayHelper::getValue($element, $this->extractKey);
    }
}
