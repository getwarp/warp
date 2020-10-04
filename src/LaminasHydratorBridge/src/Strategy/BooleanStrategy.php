<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\Strategy;

use InvalidArgumentException;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Class BooleanStrategy
 *
 * Attention: You should not extend this class because it will become final in the next major release
 * after the backward compatibility aliases are removed.
 *
 * @package spaceonfire\LaminasHydratorBridge\Strategy
 */
class BooleanStrategy implements StrategyInterface
{
    /**
     * @var array<int|string>
     */
    private $trueValue;
    /**
     * @var array<int|string>
     */
    private $falseValue;
    /**
     * @var bool
     */
    private $strict;

    /**
     * BooleanStrategy constructor.
     * @param int|string|mixed $trueValue
     * @param int|string|mixed $falseValue
     * @param bool $strict
     */
    public function __construct($trueValue, $falseValue, bool $strict = true)
    {
        $this->trueValue = $this->prepareInputValue($trueValue, '$trueValue');
        $this->falseValue = $this->prepareInputValue($falseValue, '$falseValue');
        $this->strict = $strict;
    }

    /**
     * @param mixed $inputValue
     * @param string $argument
     * @return array<int|string>
     */
    private function prepareInputValue($inputValue, string $argument): array
    {
        $result = [];

        $isIterable = is_iterable($inputValue);

        if ($isIterable && count($inputValue) === 0) {
            throw new InvalidArgumentException(sprintf('Argument %s cannot be empty iterable', $argument));
        }

        foreach ($isIterable ? $inputValue : [$inputValue] as $value) {
            if (!is_int($value) && !is_string($value)) {
                throw new InvalidArgumentException(sprintf(
                    'Argument %s expected to be int, string or iterable or int or string. Got: %s',
                    $argument,
                    is_object($value) ? get_class($value) : gettype($value)
                ));
            }

            $result[] = $value;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to extract. Expected a boolean. Got: %s.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value === true ? $this->trueValue[0] : $this->falseValue[0];
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        if (is_bool($value)) {
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
}
