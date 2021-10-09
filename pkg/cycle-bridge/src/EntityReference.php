<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Promise\ReferenceInterface;
use spaceonfire\DataSource\EntityNotFoundException;
use spaceonfire\DataSource\EntityReferenceInterface;

/**
 * @template E of object
 * @implements EntityReferenceInterface<E>
 */
final class EntityReference implements EntityReferenceInterface, PromiseInterface
{
    /**
     * @var class-string<E>
     */
    private string $class;

    /**
     * @var E|null
     */
    private ?object $entity;

    private ?ReferenceInterface $reference;

    /**
     * @param class-string<E> $class
     * @param E|null $entity
     */
    private function __construct(string $class, ?object $entity = null, ?ReferenceInterface $reference = null)
    {
        $this->class = $class;
        $this->entity = $entity;
        $this->reference = $reference;
    }

    public function __loaded(): bool
    {
        return null !== $this->entity;
    }

    /**
     * @return E
     */
    public function __resolve(): object
    {
        if (null !== $this->entity) {
            return $this->entity;
        }

        if ($this->reference instanceof PromiseInterface) {
            $entity = $this->reference->__resolve();

            if (null === $entity) {
                throw EntityNotFoundException::byPrimary($this->__role(), \implode(',', $this->__scope()));
            }

            /** @phpstan-var E $entity */
            return $this->entity = $entity;
        }

        throw new \RuntimeException('Unable to resolve reference.');
    }

    public function __role(): string
    {
        if (null !== $this->reference) {
            return $this->reference->__role();
        }

        return $this->class;
    }

    /**
     * @return array<array-key,mixed>
     */
    public function __scope(): array
    {
        if (null !== $this->reference) {
            return $this->reference->__scope();
        }

        return [];
    }

    public function getEntity(): object
    {
        return $this->__resolve();
    }

    public function equals(EntityReferenceInterface $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        if ($this->__loaded() && $other->__loaded()) {
            return $this->__resolve() === $other->__resolve();
        }

        return ($this->__role() === $other->__role() || $this->class === $other->class)
            && $this->__scope() === $other->__scope();
    }

    /**
     * @template T of object
     * @param T $entity
     * @param ReferenceInterface|null $reference
     * @return self<T>
     */
    public static function fromEntity(object $entity, ?ReferenceInterface $reference = null): self
    {
        /** @phpstan-var self<T> $ref */
        $ref = new self(\get_class($entity), $entity, $reference);
        \assert(true);
        return $ref;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param ReferenceInterface $reference
     * @return self<T>
     */
    public static function fromReference(string $class, ReferenceInterface $reference): self
    {
        /** @phpstan-var self<T> $ref */
        $ref = new self($class, null, $reference);
        \assert(true);
        return $ref;
    }
}
