<?php

declare(strict_types=1);

namespace Warp\Common\Field;

final class DefaultField implements FieldInterface
{
    private string $field;

    /**
     * @var string[]
     */
    private array $elements;

    public function __construct(string $field)
    {
        $this->field = $field;
        $this->elements = self::parseElements($field);
    }

    public function __toString()
    {
        return $this->field;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function extract($element)
    {
        $value = $element;

        foreach ($this->elements as $key) {
            $value = self::getValue($value, $key);
        }

        return $value;
    }

    /**
     * Parse extract path like in symfony property-access component.
     * @param string $field
     * @return string[]
     */
    public static function parseElements(string $field): array
    {
        if ('' === $field) {
            throw new \InvalidArgumentException('Argument #1 ($field) should be non empty string.');
        }

        $position = 0;
        $remaining = $field;

        // first element is evaluated differently - no leading dot for properties
        $pattern = '/^(([^\.\[]++)|\[([^\]]++)\])(.*)/';

        $elements = [];

        while (\preg_match($pattern, $remaining, $matches)) {
            $elements[] = '' !== $matches[2] ? $matches[2] : $matches[3];

            $position += \strlen($matches[1]);
            $remaining = $matches[4];
            $pattern = '/^(\.([^\.|\[]++)|\[([^\]]++)\])(.*)/';
        }

        if ('' !== $remaining) {
            throw new \LogicException(\sprintf(
                'Could not parse field "%s". Unexpected token "%s" at position %d.',
                $field,
                $remaining[0],
                $position,
            ));
        }

        return $elements;
    }

    /**
     * @param mixed $element
     * @param string $key
     * @return mixed
     */
    private static function getValue($element, string $key)
    {
        if (\is_array($element) && (isset($element[$key]) || \array_key_exists($key, $element))) {
            return $element[$key];
        }

        if (
            $element instanceof \ArrayAccess &&
            (isset($element[$key]) || $element->offsetExists($key))
        ) {
            return $element[$key];
        }

        if (\is_object($element)) {
            return $element->{$key};
        }

        return null;
    }
}
