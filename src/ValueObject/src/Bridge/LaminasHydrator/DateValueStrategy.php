<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Bridge\LaminasHydrator;

use InvalidArgumentException;
use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;
use spaceonfire\ValueObject\Date\DateTimeValue;
use spaceonfire\ValueObject\Date\DateTimeValueInterface;
use Webmozart\Assert\Assert;

/**
 * Class DateValueStrategy
 *
 * Attention: You should not extend this class because it will become final in the next major release
 * after the backward compatibility aliases are removed.
 *
 * @final
 */
class DateValueStrategy implements StrategyInterface
{
    /**
     * @var class-string<DateTimeValueInterface>
     */
    private $dateClass;

    /**
     * @var string
     */
    private $format;

    /**
     * DateValueStrategy constructor.
     * @param string $format
     * @param class-string<DateTimeValueInterface> $dateClass
     */
    public function __construct(string $format, string $dateClass = DateTimeImmutableValue::class)
    {
        $this->validateDateClass($dateClass);
        $this->dateClass = $dateClass;
        $this->format = $format;
    }

    /**
     * @inheritDoc
     * @param DateTimeValueInterface $value
     */
    public function extract($value, ?object $object = null)
    {
        Assert::isInstanceOf($value, $this->dateClass);
        return $value->format($this->format);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data = null)
    {
        if (!is_string($value) && !is_numeric($value)) {
            throw new InvalidArgumentException(sprintf(
                'Expected value to be a string or number. Got: "%s"',
                gettype($value)
            ));
        }

        /** @var DateTimeImmutableValue|DateTimeValue $class */
        $class = $this->dateClass;

        if (null !== $hydrated = $class::createFromFormat($this->format, (string)$value)) {
            return $hydrated;
        }

        $hydrated = $class::createFromFormat($this->format, $class::from($value)->format($this->format));

        \assert(null !== $hydrated);

        return $hydrated;
    }

    private function validateDateClass(string $dateClass): void
    {
        $allowedClasses = [DateTimeImmutableValue::class, DateTimeValue::class];

        foreach ($allowedClasses as $allowedClass) {
            if ($dateClass === $allowedClass || is_subclass_of($dateClass, $allowedClass)) {
                return;
            }
        }

        $message = 'Expected $dateClass to be one of: ' . implode(', ', $allowedClasses) .
            ' or a sub-class of them. Got: ' . $dateClass;

        throw new InvalidArgumentException($message);
    }
}
