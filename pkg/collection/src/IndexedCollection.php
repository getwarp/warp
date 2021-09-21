<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use InvalidArgumentException;

final class IndexedCollection extends AbstractCollectionDecorator
{
    /**
     * @var callable|string
     */
    private $indexer;

    /**
     * IndexedCollection constructor.
     * @param CollectionInterface|array|iterable|mixed $items
     * @param callable|string $indexer
     */
    public function __construct($items, $indexer)
    {
        parent::__construct($items);

        if (!is_string($indexer) && !is_callable($indexer)) {
            throw new InvalidArgumentException('Argument $indexer must be of type string or callable.');
        }

        $this->indexer = $indexer;
        $this->collection = $this->collection->indexBy($this->indexer);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        parent::offsetSet(ArrayHelper::getValue($value, $this->indexer), $value);
    }

    /**
     * @inheritDoc
     */
    public function keys(): CollectionInterface
    {
        return $this->downgrade()->keys();
    }

    /**
     * @inheritDoc
     */
    public function flip(): CollectionInterface
    {
        return $this->downgrade()->flip();
    }

    /**
     * @inheritDoc
     * Also collection will be downgraded
     */
    public function remap($from, $to): CollectionInterface
    {
        return $this->downgrade()->remap($from, $to);
    }

    /**
     * @inheritDoc
     */
    protected function newStatic($items)
    {
        return new self($items, $this->indexer);
    }
}
