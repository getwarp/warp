<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Warp\Clock\DateTimeImmutableValue;
use Warp\Clock\DateTimeValue;
use Warp\Clock\DateTimeValueInterface;

/**
 * @template T of DateTimeValue|DateTimeImmutableValue
 */
final class DateValueStrategy implements StrategyInterface
{
    /**
     * @var class-string<T>
     */
    private string $dateClass;
    private string $format;
    private \DateTimeZone $timezone;

    /**
     * @param string $format
     * @param class-string<T> $dateClass
     * @param \DateTimeZone|string|null $timezone
     */
    public function __construct(
        string $format,
        string $dateClass = DateTimeImmutableValue::class,
        $timezone = null
    ) {
        if (!\is_subclass_of($dateClass, DateTimeValueInterface::class)) {
            throw new \InvalidArgumentException(\sprintf(
                'Argument #2 ($dateClass) expected to be subclass of %s. Got: %s.',
                DateTimeValueInterface::class,
                $dateClass,
            ));
        }

        $this->dateClass = $dateClass;
        $this->format = $format;

        if ($timezone instanceof \DateTimeZone) {
            $this->timezone = $timezone;
        } elseif (\is_string($timezone) || null === $timezone) {
            $this->timezone = new \DateTimeZone($timezone ?: \date_default_timezone_get());
        } else {
            throw new \InvalidArgumentException(\sprintf(
                'Argument #3 ($timezone) expected to be instance of %s, string or null. Got: %s.',
                \DateTimeZone::class,
                \get_debug_type($timezone),
            ));
        }
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

        return $this->makeDateValueObject($value)->format($this->format);
    }

    /**
     * @inheritDoc
     * @param array<string,mixed>|null $data
     * @return T
     */
    public function hydrate($value, ?array $data = null)
    {
        if ($value instanceof \DateTimeInterface) {
            return $this->makeDateValueObject($value);
        }

        $date = \DateTimeImmutable::createFromFormat($this->format, (string)$value, $this->timezone);
        // @phpstan-ignore-next-line
        return $date ? $this->dateClass::from($date) : $this->makeDateValueObject($value);
    }

    /**
     * @param \DateTimeInterface|string|int $value
     * @return T
     */
    private function makeDateValueObject($value): DateTimeValueInterface
    {
        if ($value instanceof $this->dateClass && 0 === $this->timezone->getOffset($value)) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            $date = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s.u',
                $value->format('Y-m-d H:i:s.u'),
                $value->getTimezone()
            );
            \assert(false !== $date);
            $date = $date->setTimezone($this->timezone);

            // @phpstan-ignore-next-line
            return $this->dateClass::from($date);
        }

        $timestamp = \filter_var($value, \FILTER_VALIDATE_INT);
        if ($timestamp) {
            return new $this->dateClass('@' . $timestamp, $this->timezone);
        }

        $value = \trim((string)$value);
        if ('' === $value) {
            throw new \InvalidArgumentException('Unable to create date from empty string.');
        }

        return new $this->dateClass($value, $this->timezone);
    }
}
