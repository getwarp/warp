<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use InvalidArgumentException;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;
use spaceonfire\ValueObject\Date\DateTimeValue;
use spaceonfire\ValueObject\Date\DateTimeValueInterface;
use Webmozart\Assert\Assert;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class DateValueZendHydratorStrategy
 * @package spaceonfire\ValueObject\Integrations\HydratorStrategy
 * @codeCoverageIgnore
 */
final class DateValueZendHydratorStrategy implements StrategyInterface
{
    /**
     * @var string|DateTimeValueInterface
     */
    private $dateClass;
    /**
     * @var string
     */
    private $format;

    /**
     * DateValueZendHydratorStrategy constructor.
     * @param string $format
     * @param string|DateTimeValueInterface $dateClass
     */
    public function __construct(string $format, string $dateClass = DateTimeImmutableValue::class)
    {
        $this->validateDateClass($dateClass);
        $this->dateClass = $dateClass;
        $this->format = $format;
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
    public function hydrate($value, ?array $data)
    {
        /** @var DateTimeImmutableValue|DateTimeValue $class */
        $class = $this->dateClass;
        return $class::createFromFormat($this->format, $value);
    }
}
