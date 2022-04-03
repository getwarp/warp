<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Blame;

use spaceonfire\Clock\DateTimeImmutableValue;
use spaceonfire\DataSource\EntityReferenceInterface;

/**
 * @template T of object
 */
interface BlameImmutableInterface
{
    public function isNew(): bool;

    public function isTouched(): bool;

    public function getCreatedAt(): DateTimeImmutableValue;

    public function getUpdatedAt(): DateTimeImmutableValue;

    /**
     * @return T|null
     */
    public function getCreatedBy(): ?object;

    /**
     * @return T|null
     */
    public function getUpdatedBy(): ?object;

    /**
     * @param string[] $fields
     * @return array{createdAt?:DateTimeImmutableValue,createdBy?:T|EntityReferenceInterface<T>|null,updatedAt?:DateTimeImmutableValue,updatedBy?:T|EntityReferenceInterface<T>|null}
     */
    public function toArray(array $fields = []): array;
}
