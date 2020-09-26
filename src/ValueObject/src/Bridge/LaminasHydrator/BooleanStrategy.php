<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Bridge\LaminasHydrator;

use InvalidArgumentException;
use Laminas\Hydrator\Strategy\StrategyInterface;
use Webmozart\Assert\Assert;

/**
 * Class BooleanStrategy
 *
 * Attention: You should not extend this class because it will become final in the next major release
 * after the backward compatibility aliases are removed.
 *
 * @package spaceonfire\ValueObject\Bridge\LaminasHydrator
 */
class BooleanStrategy implements StrategyInterface
{
    /**
     * @var int|string
     */
    private $trueValue;
    /**
     * @var int|string
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
        if (!is_int($trueValue) && !is_string($trueValue)) {
            throw new InvalidArgumentException(sprintf(
                'Expected int or string as $trueValue. Got: %s',
                is_object($trueValue) ? get_class($trueValue) : gettype($trueValue)
            ));
        }

        if (!is_int($falseValue) && !is_string($falseValue)) {
            throw new InvalidArgumentException(sprintf(
                'Expected int or string as $falseValue. Got: %s',
                is_object($falseValue) ? get_class($falseValue) : gettype($falseValue)
            ));
        }

        $this->trueValue = $trueValue;
        $this->falseValue = $falseValue;
        $this->strict = $strict;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        Assert::boolean($value, 'Unable to extract. Expected a boolean. Got: %s.');
        return $value === true ? $this->trueValue : $this->falseValue;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($this->strict) {
            return $value === $this->trueValue;
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return $value == $this->trueValue;
    }
}
