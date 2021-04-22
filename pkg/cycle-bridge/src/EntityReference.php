<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Promise\PromiseInterface;
use Cycle\ORM\Promise\ReferenceInterface;
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
        return $this->getEntity();
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

        throw new \RuntimeException('Reference has no scope.');
    }

    public function getEntity(): object
    {
        if (null !== $this->entity) {
            return $this->entity;
        }

        if ($this->reference instanceof PromiseInterface) {
            return $this->entity = $this->reference->__resolve();
        }

        throw new \RuntimeException('Unable to resolve reference.');
    }

    /**
     * @param E $entity
     * @param ReferenceInterface|null $reference
     * @return self<E>
     */
    public static function fromEntity(object $entity, ?ReferenceInterface $reference = null): self
    {
        return new self(\get_class($entity), $entity, $reference);
    }

    /**
     * @param class-string<E> $class
     * @param ReferenceInterface $reference
     * @return self<E>
     */
    public static function fromReference(string $class, ReferenceInterface $reference): self
    {
        return new self($class, null, $reference);
    }
}
