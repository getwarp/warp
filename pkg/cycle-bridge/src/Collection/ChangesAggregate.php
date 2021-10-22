<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

/**
 * @template T of object
 * @template P
 * @implements \IteratorAggregate<Change<T,P>>
 */
final class ChangesAggregate implements \IteratorAggregate
{
    /**
     * @var \SplObjectStorage<T,Change<T,P>>
     */
    private \SplObjectStorage $added;

    /**
     * @var \SplObjectStorage<T,Change<T,P>>
     */
    private \SplObjectStorage $removed;

    private function __construct()
    {
        $this->added = new \SplObjectStorage();
        $this->removed = new \SplObjectStorage();
    }

    public function __clone()
    {
        /** @phpstan-var \SplObjectStorage<T,Change<T,P>> $added */
        $added = new \SplObjectStorage();
        $added->addAll($this->added);
        $this->added = $added;

        /** @phpstan-var \SplObjectStorage<T,Change<T,P>> $removed */
        $removed = new \SplObjectStorage();
        $removed->addAll($this->removed);
        $this->removed = $removed;
    }

    /**
     * @param iterable<Change<T,P>> $changes
     * @return self<T,P>
     */
    public static function new(iterable $changes = []): self
    {
        /** @phpstan-var self<T,P> $player */
        $player = new self();
        foreach ($changes as $change) {
            $player->recordChanges($change);
        }
        return $player;
    }

    /**
     * @param T $element
     * @return Change<T,P>|null
     */
    public function get(object $element): ?Change
    {
        return $this->added[$element] ?? $this->removed[$element];
    }

    public function hasChanges(): bool
    {
        return 0 < $this->added->count() || 0 < $this->removed->count();
    }

    /**
     * @param Change<T,P> $change
     * @param Change<T,P> ...$changes
     */
    public function recordChanges(Change $change, Change ...$changes): void
    {
        foreach ([$change, ...$changes] as $item) {
            $element = $item->getElement();

            switch ($item->getType()) {
                case Change::ADD:
                    $this->removed->detach($element);
                    $this->added->attach($element, $item);
                    break;

                case Change::REMOVE:
                    $this->added->detach($element);
                    $this->removed->attach($element, $item);
                    break;

                default:
                    throw new \RuntimeException(\sprintf('Unknown change type: %s.', $item->getType()));
            }
        }
    }

    public function clean(): void
    {
        $this->added = new \SplObjectStorage();
        $this->removed = new \SplObjectStorage();
    }

    /**
     * @return \Generator<Change<T,P>>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->added as $object) {
            yield $this->added[$object];
        }

        foreach ($this->removed as $object) {
            yield $this->removed[$object];
        }
    }
}
