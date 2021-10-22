<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;
use spaceonfire\Bridge\Cycle\Collection\ObjectStorage;

/**
 * @template T of object
 * @template P
 * @implements ObjectCollectionInterface<T,P>
 * @extends ArrayCollection<array-key,T>
 */
final class DoctrineObjectCollection extends ArrayCollection implements ObjectCollectionInterface
{
    /**
     * @var ObjectStorage<T,P|null>
     */
    private ObjectStorage $storage;

    /**
     * @param iterable<array-key,T> $elements
     * @param \SplObjectStorage<T,P|null>|null $storage
     */
    public function __construct(iterable $elements = [], ?\SplObjectStorage $storage = null)
    {
        // @phpstan-ignore-next-line
        $this->storage = ObjectStorage::snapshot($storage ?? $elements);
        $elements = \is_array($elements) ? $elements : \iterator_to_array($elements);

        parent::__construct($elements);
    }

    public function clear(): void
    {
        parent::clear();

        // @phpstan-ignore-next-line
        $this->storage = new ObjectStorage();
    }

    public function remove($key)
    {
        $element = parent::remove($key);

        if (null !== $element) {
            $this->storage->detach($element);
        }

        return $element;
    }

    public function removeElement($element)
    {
        $this->storage->detach($element);

        return parent::removeElement($element);
    }

    public function add($element)
    {
        $this->storage->attach($element);
        return parent::add($element);
    }

    public function set($key, $value): void
    {
        $oldValue = $this->get($key);

        if ($oldValue === $value) {
            return;
        }

        parent::set($key, $value);

        $this->storage->attach($value, null === $oldValue ? null : $this->storage->getPivot($oldValue));
        if (null !== $oldValue) {
            $this->storage->detach($oldValue);
        }
    }

    public function hasPivot(object $element): bool
    {
        return $this->storage->hasPivot($element);
    }

    public function getPivot(object $element)
    {
        return $this->storage->getPivot($element);
    }

    public function setPivot(object $element, $pivot): void
    {
        $this->storage->setPivot($element, $pivot);
    }

    public function getPivotContext(): \SplObjectStorage
    {
        return $this->storage->getPivotContext();
    }

    /**
     * @param array<T> $elements
     * @return self<T,P>
     */
    protected function createFrom(array $elements): self
    {
        return new self($elements, self::filterStorage($this->storage->getPivotContext(), $elements));
    }

    /**
     * @param \SplObjectStorage<T,P|null> $storage
     * @param T[] $elements
     * @return \SplObjectStorage<T,P|null>
     */
    private static function filterStorage(\SplObjectStorage $storage, array $elements): \SplObjectStorage
    {
        /** @phpstan-var \SplObjectStorage<T,P|null> $output */
        $output = new \SplObjectStorage();

        foreach ($elements as $element) {
            $output->attach($element, $storage[$element] ?? null);
        }

        return $output;
    }
}
