<?php

declare(strict_types=1);

namespace Warp\DataSource\Blame;

use Warp\Clock\DateTimeImmutableValue;

/**
 * @template T of object
 * @implements BlameImmutableInterface<T>
 */
final class BlameImmutable implements BlameImmutableInterface
{
    /**
     * @var BlameImmutableInterface<T>
     */
    private BlameImmutableInterface $blame;

    /**
     * @param BlameImmutableInterface<T> $blame
     */
    public function __construct(BlameImmutableInterface $blame)
    {
        $this->blame = $blame;
    }

    public function isNew(): bool
    {
        return $this->blame->isNew();
    }

    public function isTouched(): bool
    {
        return $this->blame->isTouched();
    }

    public function getCreatedAt(): DateTimeImmutableValue
    {
        return $this->blame->getCreatedAt();
    }

    public function getUpdatedAt(): DateTimeImmutableValue
    {
        return $this->blame->getUpdatedAt();
    }

    public function getCreatedBy(): ?object
    {
        return $this->blame->getCreatedBy();
    }

    public function getUpdatedBy(): ?object
    {
        return $this->blame->getUpdatedBy();
    }

    public function toArray(array $fields = []): array
    {
        return $this->blame->toArray();
    }
}
