<?php

declare(strict_types=1);

namespace spaceonfire\Container\Definition;

use spaceonfire\Collection\AbstractCollectionDecorator;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Collection\IndexedCollection;
use spaceonfire\Collection\TypedCollection;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Type\InstanceOfType;

final class DefinitionAggregate extends AbstractCollectionDecorator implements DefinitionAggregateInterface
{
    /**
     * @var DefinitionFactoryInterface
     */
    private $definitionFactory;

    /**
     * DefinitionAggregate constructor.
     * @param DefinitionInterface[] $items
     * @param DefinitionFactoryInterface|null $definitionFactory
     */
    public function __construct($items = [], ?DefinitionFactoryInterface $definitionFactory = null)
    {
        $this->definitionFactory = $definitionFactory ?? new DefinitionFactory();

        parent::__construct(
            new IndexedCollection(
                new TypedCollection($items, new InstanceOfType(DefinitionInterface::class)),
                [$this, 'indexer']
            )
        );
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     * @return self
     */
    protected function newStatic($items): CollectionInterface
    {
        return new self($items, $this->definitionFactory);
    }

    /**
     * Returns index for definition
     * @param DefinitionInterface $value
     * @return string
     */
    public function indexer(DefinitionInterface $value): string
    {
        return $value->getAbstract();
    }

    /**
     * @inheritDoc
     * @param DefinitionInterface $value
     */
    public function offsetSet($_, $value): void
    {
        $alias = $this->indexer($value);

        if ($this->offsetExists($alias)) {
            throw new ContainerException(sprintf('Alias (%s) definition already defined', $alias));
        }

        parent::offsetSet(null, $value);
    }

    /**
     * @inheritDoc
     */
    public function addDefinition(DefinitionInterface $definition): DefinitionAggregateInterface
    {
        $this->offsetSet(null, $definition);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasDefinition(string $id): bool
    {
        return $this->offsetExists($id);
    }

    /**
     * @inheritDoc
     */
    public function getDefinition(string $id): DefinitionInterface
    {
        if (!$this->hasDefinition($id)) {
            throw new ContainerException(sprintf('Definition for alias (%s) not found', $id));
        }

        return $this->offsetGet($id);
    }

    /**
     * @inheritDoc
     */
    public function makeDefinition(string $abstract, $concrete, bool $shared = false): DefinitionInterface
    {
        return $this->definitionFactory->make($abstract, $concrete, $shared);
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $id, ContainerInterface $container)
    {
        return $this->getDefinition($id)->resolve($container);
    }
}
