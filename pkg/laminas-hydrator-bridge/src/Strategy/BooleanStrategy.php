<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

final class BooleanStrategy implements StrategyInterface
{
    /**
     * @var array<int|string>
     */
    private array $trueValue;

    /**
     * @var array<int|string>
     */
    private array $falseValue;

    private bool $strict;

    /**
     * @param int|string|array<int|string> $trueValue
     * @param int|string|array<int|string> $falseValue
     * @param bool $strict
     */
    public function __construct($trueValue, $falseValue, bool $strict = true)
    {
        $this->trueValue = self::prepareInputValue($trueValue, '$trueValue');
        $this->falseValue = self::prepareInputValue($falseValue, '$falseValue');
        $this->strict = $strict;
    }

    public function extract($value, ?object $object = null)
    {
        if (!\is_bool($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unable to extract. Expected a boolean. Got: %s.',
                \get_debug_type($value)
            ));
        }

        return true === $value ? $this->trueValue[0] : $this->falseValue[0];
    }

    /**
     * @inheritDoc
     * @param array<string,mixed>|null $data
     */
    public function hydrate($value, ?array $data = null): bool
    {
        if (\is_bool($value)) {
            return $value;
        }

        foreach ($this->trueValue as $trueValue) {
            /** @noinspection TypeUnsafeComparisonInspection */
            if (($this->strict ? $value === $trueValue : $value == $trueValue)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $inputValue
     * @param string $argument
     * @return array<int|string>
     */
    private static function prepareInputValue($inputValue, string $argument): array
    {
        $result = [];

        foreach (\is_iterable($inputValue) ? $inputValue : [$inputValue] as $value) {
            if (!\is_int($value) && !\is_string($value)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Argument %s expected to be int, string or iterator of int or string. Got: %s',
                    $argument,
                    \is_object($value) ? \get_class($value) : \gettype($value)
                ));
            }

            $result[] = $value;
        }

        if (0 === \count($result)) {
            throw new \InvalidArgumentException(\sprintf('Argument %s cannot be empty iterable', $argument));
        }

        return $result;
    }
}
