<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Warp\Clock\DateTimeImmutableValue;
use Warp\Clock\DateTimeValueInterface;

/**
 * @template T of DateTimeValueInterface
 */
final class DateValueStrategy implements StrategyInterface
{
    /**
     * @var class-string<T>
     */
    private string $dateClass;

    private string $format;

    /**
     * @param string $format
     * @param class-string<T> $dateClass
     */
    public function __construct(string $format, string $dateClass = DateTimeImmutableValue::class)
    {
        if (!\is_subclass_of($dateClass, DateTimeValueInterface::class)) {
            throw new \InvalidArgumentException(\sprintf(
                'Argument #2 ($dateClass) expected to be subclass of %s. Got: %s.',
                DateTimeValueInterface::class,
                $dateClass,
            ));
        }

        $this->dateClass = $dateClass;
        $this->format = $format;
    }

    /**
     * @inheritDoc
     * @param T $value
     * @param object|null $object
     * @return string
     */
    public function extract($value, ?object $object = null): string
    {
        if (!$value instanceof DateTimeValueInterface) {
            throw new \InvalidArgumentException(\sprintf(
                'Unable to extract. Expected instance of %s. Got: %s.',
                DateTimeValueInterface::class,
                \get_debug_type($value),
            ));
        }

        return $value->format($this->format);
    }

    /**
     * @inheritDoc
     * @param array<string,mixed>|null $data
     * @return T
     */
    public function hydrate($value, ?array $data = null)
    {
        if ($value instanceof \DateTimeInterface) {
            return $this->dateClass::from($value);
        }

        if (!\is_string($value) && !\is_numeric($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Expected value to be a string or number. Got: %s.',
                \get_debug_type($value),
            ));
        }

        $date = DateTimeImmutableValue::createFromFormat($this->format, (string)$value)
            ?? DateTimeImmutableValue::createFromFormat(
                $this->format,
                DateTimeImmutableValue::from($value)->format($this->format)
            );

        \assert(null !== $date);

        return $this->dateClass::from($date);
    }
}
