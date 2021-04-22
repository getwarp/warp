<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Blame;

use spaceonfire\Common\Factory\StaticConstructorInterface;
use spaceonfire\DataSource\EntityReferenceInterface;
use spaceonfire\ValueObject\Date\ClockInterface;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;
use spaceonfire\ValueObject\Date\FrozenClock;
use spaceonfire\ValueObject\Date\SystemClock;

/**
 * @template T of object
 * @implements BlameInterface<T>
 */
final class Blame implements BlameInterface, StaticConstructorInterface
{
    private ClockInterface $clock;

    private DateTimeImmutableValue $createdAt;

    private DateTimeImmutableValue $updatedAt;

    /**
     * @var class-string<T>|null
     */
    private ?string $actorClass;

    /**
     * @var T|EntityReferenceInterface<T>|null
     */
    private $createdBy;

    /**
     * @var T|EntityReferenceInterface<T>|null
     */
    private $updatedBy;

    /**
     * @param class-string<T>|null $actorClass
     * @param DateTimeImmutableValue|null $createdAt
     * @param DateTimeImmutableValue|null $updatedAt
     * @param T|EntityReferenceInterface<T>|null $createdBy
     * @param T|EntityReferenceInterface<T>|null $updatedBy
     * @param ClockInterface|null $clock
     */
    private function __construct(
        ?string $actorClass = null,
        ?DateTimeImmutableValue $createdAt = null,
        ?DateTimeImmutableValue $updatedAt = null,
        ?object $createdBy = null,
        ?object $updatedBy = null,
        ?ClockInterface $clock = null
    ) {
        $this->actorClass = $actorClass;
        $this->clock = $clock ?? new FrozenClock(SystemClock::fromUTC());
        $this->createdAt = $createdAt ?? $this->clock->now();
        $this->updatedAt = $updatedAt ?? $this->createdAt;

        $this->assertActor($createdBy, true);
        $this->assertActor($updatedBy, true);

        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy ?? $this->createdBy;
    }

    /**
     * @param class-string<T>|null $actorClass
     * @param DateTimeImmutableValue|null $createdAt
     * @param T|null $createdBy
     * @param ClockInterface|null $clock
     * @return self<T>
     */
    public static function new(
        ?string $actorClass = null,
        ?DateTimeImmutableValue $createdAt = null,
        ?object $createdBy = null,
        ?ClockInterface $clock = null
    ): self {
        return new self($actorClass, $createdAt, null, $createdBy, null, $clock);
    }

    /**
     * @param array{createdAt?:DateTimeImmutableValue,createdBy?:T|EntityReferenceInterface<T>|null,updatedAt?:DateTimeImmutableValue,updatedBy?:T|EntityReferenceInterface<T>|null} $data
     * @param class-string<T>|null $actorClass
     * @param ClockInterface|null $clock
     * @return self<T|object>
     */
    public static function fromArray(array $data, ?string $actorClass = null, ?ClockInterface $clock = null): self
    {
        return new self(
            $actorClass,
            isset($data['createdAt']) ? DateTimeImmutableValue::from($data['createdAt']) : null,
            isset($data['updatedAt']) ? DateTimeImmutableValue::from($data['updatedAt']) : null,
            $data['createdBy'] ?? null,
            $data['updatedBy'] ?? null,
            $clock,
        );
    }

    public function toArray(array $fields = []): array
    {
        $fields = 0 !== \count($fields) ? \array_flip($fields) : [
            'createdAt' => null,
            'updatedAt' => null,
            'createdBy' => null,
            'updatedBy' => null,
        ];

        $output = [];

        if (\array_key_exists('createdAt', $fields)) {
            $output['createdAt'] = $this->createdAt;
        }
        if (\array_key_exists('updatedAt', $fields)) {
            $output['updatedAt'] = $this->updatedAt;
        }
        if (\array_key_exists('createdBy', $fields)) {
            $output['createdBy'] = $this->createdBy;
        }
        if (\array_key_exists('updatedBy', $fields)) {
            $output['updatedBy'] = $this->updatedBy;
        }

        return $output;
    }

    public function touch(?object $by = null): void
    {
        $this->assertActor($by, false);
        $this->updatedAt = $this->clock->now();

        if ($this->isNew()) {
            $this->createdBy ??= $by;
        }

        $this->updatedBy ??= $by;
    }

    public function isNew(): bool
    {
        return $this->createdAt === $this->updatedAt;
    }

    public function isTouched(): bool
    {
        if ($this->isNew()) {
            return false;
        }

        return $this->updatedAt === $this->clock->now();
    }

    public function getCreatedAt(): DateTimeImmutableValue
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutableValue
    {
        return $this->updatedAt;
    }

    public function getCreatedBy(): ?object
    {
        return $this->createdBy = $this->resolveActor($this->createdBy);
    }

    public function getUpdatedBy(): ?object
    {
        return $this->updatedBy = $this->resolveActor($this->updatedBy);
    }

    /**
     * @param T|EntityReferenceInterface<T>|null $actor
     * @param bool $acceptReference
     */
    private function assertActor($actor, bool $acceptReference): void
    {
        if (null === $actor) {
            return;
        }

        if ($actor instanceof EntityReferenceInterface && $acceptReference) {
            return;
        }

        if (null === $this->actorClass || $actor instanceof $this->actorClass) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Expected actor to be instance of %s. Got: %s.',
            $this->actorClass,
            \get_debug_type($actor),
        ));
    }

    /**
     * @param T|EntityReferenceInterface<T>|null $actor
     * @return T|null
     */
    private function resolveActor($actor): ?object
    {
        if ($actor instanceof EntityReferenceInterface) {
            $actor = $actor->getEntity();
        }

        if (null === $actor) {
            return null;
        }

        if (null === $this->actorClass) {
            /** @phpstan-var T $actor */
            return $actor;
        }

        if ($actor instanceof $this->actorClass) {
            return $actor;
        }

        throw new \RuntimeException(\sprintf(
            'Expected actor to be instance of %s. Got: %s.',
            $this->actorClass,
            \get_debug_type($actor),
        ));
    }
}
