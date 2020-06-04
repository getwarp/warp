<?php

declare(strict_types=1);

namespace spaceonfire\Container\ServiceProvider;

use spaceonfire\Collection\AbstractCollectionDecorator;
use spaceonfire\Collection\IndexedCollection;
use spaceonfire\Collection\TypedCollection;
use spaceonfire\Container\ContainerAwareTrait;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Type\InstanceOfType;

final class ServiceProviderAggregate extends AbstractCollectionDecorator implements ServiceProviderAggregateInterface
{
    use ContainerAwareTrait;

    /**
     * @var array<string,string> maps service name to provider id which provides it
     */
    private $providesMap = [];
    /**
     * @var array<string,bool>
     */
    private $registered = [];

    /**
     * ServiceProviderAggregate constructor.
     * @param ServiceProviderInterface[] $items
     */
    public function __construct($items = [])
    {
        parent::__construct(
            new IndexedCollection(
                new TypedCollection($items, new InstanceOfType(ServiceProviderInterface::class)),
                [$this, 'indexer']
            )
        );
    }

    /**
     * Returns index for definition
     * @param ServiceProviderInterface $value
     * @return string
     */
    public function indexer(ServiceProviderInterface $value): string
    {
        return $value->getIdentifier();
    }

    /**
     * @inheritDoc
     * @param ServiceProviderInterface $value
     */
    public function offsetSet($_, $value): void
    {
        $alias = $this->indexer($value);

        if ($this->offsetExists($alias)) {
            throw new ContainerException(sprintf('Provider with "%s" identifier is already exists.', $alias));
        }

        $value->setContainer($this->getContainer());

        if ($value instanceof BootableServiceProviderInterface) {
            $value->boot();
        }

        foreach ($value->provides() as $service) {
            if (!isset($this->providesMap[$service])) {
                $this->providesMap[$service] = $alias;
            }
        }

        parent::offsetSet(null, $value);
    }

    /**
     * @inheritDoc
     */
    public function addProvider(ServiceProviderInterface $provider): ServiceProviderAggregateInterface
    {
        if ($this->offsetExists($provider->getIdentifier())) {
            return $this;
        }

        $this->offsetSet(null, $provider);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function provides(string $service): bool
    {
        return array_key_exists($service, $this->providesMap);
    }

    /**
     * @inheritDoc
     */
    public function register(string $service): void
    {
        if (false === $this->provides($service)) {
            throw new ContainerException(sprintf('(%s) is not provided by a service provider', $service));
        }

        $providerId = $this->providesMap[$service];

        if (array_key_exists($providerId, $this->registered)) {
            return;
        }

        /** @var ServiceProviderInterface $provider */
        $provider = $this->offsetGet($providerId);
        $provider->register();
        $this->registered[$provider->getIdentifier()] = true;
    }
}
