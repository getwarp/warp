<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\DefaultStrategy;
use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\DataSource\Blame\Blame;
use spaceonfire\DataSource\Blame\BlameImmutableInterface;
use spaceonfire\ValueObject\Date\ClockInterface;

/**
 * @template T of object
 */
final class BlameStrategy implements StrategyInterface
{
    /**
     * @var class-string<T>|null
     */
    private ?string $actorClass;

    /**
     * @var string[]
     */
    private array $fields;

    private ?ClockInterface $clock;

    private StrategyInterface $dateStrategy;

    /**
     * @param class-string<T>|null $actorClass
     * @param string[] $fields
     * @param StrategyInterface|null $dateStrategy
     * @param ClockInterface|null $clock
     */
    public function __construct(
        ?string $actorClass = null,
        array $fields = [],
        ?StrategyInterface $dateStrategy = null,
        ?ClockInterface $clock = null
    ) {
        $this->actorClass = $actorClass;
        $this->fields = $fields;
        $this->clock = $clock;
        $this->dateStrategy = $dateStrategy ?? new DefaultStrategy();
    }

    public function extract($value, ?object $object = null)
    {
        if (!$value instanceof BlameImmutableInterface) {
            throw new \InvalidArgumentException(\sprintf(
                'Unable to extract. Expected instance of %s. Got: %s.',
                BlameImmutableInterface::class,
                \get_debug_type($value),
            ));
        }

        $output = $value->toArray($this->fields);

        if (isset($output['createdAt'])) {
            $output['createdAt'] = $this->dateStrategy->extract($output['createdAt']);
        }

        if (isset($output['updatedAt'])) {
            $output['updatedAt'] = $this->dateStrategy->extract($output['updatedAt']);
        }

        return $output;
    }

    /**
     * @param mixed $value
     * @param array<array-key,mixed>|null $data
     * @return BlameImmutableInterface<T|object>
     */
    public function hydrate($value, ?array $data = null): BlameImmutableInterface
    {
        if ($value instanceof BlameImmutableInterface) {
            return $value;
        }

        if (!\is_array($value)) {
            throw new \InvalidArgumentException(\sprintf(
                'Expected value to be an array. Got: %s.',
                \get_debug_type($value),
            ));
        }

        if (isset($value['createdAt'])) {
            $value['createdAt'] = $this->dateStrategy->hydrate($value['createdAt'], null);
        }

        if (isset($value['updatedAt'])) {
            $value['updatedAt'] = $this->dateStrategy->hydrate($value['updatedAt'], null);
        }

        return Blame::fromArray($value, $this->actorClass, $this->clock);
    }
}
