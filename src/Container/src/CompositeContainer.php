<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use Generator;
use IteratorAggregate;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use spaceonfire\Collection\ArrayHelper;
use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Container\Definition\DefinitionInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;
use Traversable;

/**
 * Class CompositeContainer
 *
 * Attention: You should not extend this class because it will become final in the next major release
 * after the backward compatibility aliases are removed.
 *
 * @package spaceonfire\Container
 * @final
 */
class CompositeContainer implements ContainerWithServiceProvidersInterface, ContainerAwareInterface, IteratorAggregate
{
    use ContainerAwareTrait;

    private const DEFAULT_PRIORITY = 999;

    /**
     * @var array<int, PsrContainerInterface[]>
     */
    private $containers = [];
    /**
     * @var ContainerInterface|null
     */
    private $primary;

    /**
     * CompositeContainer constructor.
     * @param PsrContainerInterface[] $containers
     */
    public function __construct(iterable $containers = [])
    {
        $this->setContainer($this);
        $this->addContainers($containers);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;
        foreach ($this->getIterator() as $delegate) {
            if ($delegate instanceof ContainerAwareInterface) {
                $delegate->setContainer($container);
            }
        }
        return $this;
    }


    /**
     * Getter for `primary` property
     * @return ContainerInterface
     */
    private function getPrimary(): ContainerInterface
    {
        if ($this->primary === null) {
            throw new ContainerException('No primary container provided with support of definitions');
        }

        return $this->primary;
    }

    /**
     * Setter for `primary` property
     * @param ContainerInterface $primary
     */
    private function setPrimary(ContainerInterface $primary): void
    {
        if (
            $this->primary === null ||
            (
                !$this->primary instanceof ContainerWithServiceProvidersInterface &&
                $primary instanceof ContainerWithServiceProvidersInterface
            )
        ) {
            $this->primary = $primary;
        }
    }

    /**
     * Add containers to the chain
     * @param PsrContainerInterface[] $containers
     * @return $this
     */
    public function addContainers(iterable $containers): self
    {
        if ($containers instanceof Traversable) {
            $containers = iterator_to_array($containers);
        }

        $isAssoc = ArrayHelper::isArrayAssoc($containers);

        foreach ($containers as $priority => $container) {
            if (is_int($priority)) {
                // Assoc means that the keys are not in order.
                // So if this is integer key we threat it as priority.
                // In other way add gaps between keys
                $priority = $isAssoc ? $priority : ($priority + 1) * 10;
            } else {
                $priority = null;
            }

            $this->addContainer($container, $priority ?? self::DEFAULT_PRIORITY);
        }
        return $this;
    }

    /**
     * Add container to the chain
     * @param PsrContainerInterface $container
     * @param int $priority
     * @return $this
     */
    public function addContainer(PsrContainerInterface $container, int $priority = self::DEFAULT_PRIORITY): self
    {
        if ($container instanceof ContainerInterface) {
            $this->setPrimary($container);
        }

        if ($container instanceof ContainerAwareInterface) {
            $container->setContainer($this->getContainer());
        }

        if (isset($this->containers[$priority])) {
            $this->containers[$priority][] = $container;
        } else {
            $this->containers[$priority] = [$container];
        }

        return $this;
    }

    /**
     * Iterates over inner containers
     * @return Generator<PsrContainerInterface>
     */
    public function getIterator(): Generator
    {
        // Sort by priority
        ksort($this->containers);

        foreach ($this->containers as $containers) {
            foreach ($containers as $container) {
                yield $container;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $arguments = [])
    {
        foreach ($this->getIterator() as $container) {
            if ($container->has($id)) {
                return $container instanceof ContainerInterface
                    ? $container->get($id, $arguments)
                    : $container->get($id);
            }
        }

        throw new NotFoundException(sprintf('Alias (%s) is not being managed by any container in chain', $id));
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        foreach ($this->getIterator() as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function make(string $alias, array $arguments = [])
    {
        return $this->getPrimary()->make($alias, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function invoke(callable $callable, array $arguments = [])
    {
        return $this->getPrimary()->invoke($callable, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function add(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        return $this->getPrimary()->add($id, $concrete, $shared);
    }

    /**
     * @inheritDoc
     */
    public function share(string $id, $concrete = null): DefinitionInterface
    {
        return $this->getPrimary()->share($id, $concrete);
    }

    /**
     * @inheritDoc
     */
    public function addServiceProvider($provider): ContainerWithServiceProvidersInterface
    {
        $primary = $this->getPrimary();

        if ($primary instanceof ContainerWithServiceProvidersInterface) {
            $primary->addServiceProvider($provider);
            return $this;
        }

        throw new ContainerException('No container provided with support of service providers');
    }

    /**
     * @inheritDoc
     */
    public function hasTagged(string $tag): bool
    {
        foreach ($this->getIterator() as $container) {
            if ($container instanceof ContainerInterface && $container->hasTagged($tag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getTagged(string $tag): CollectionInterface
    {
        $result = new Collection();

        foreach ($this->getIterator() as $container) {
            if (!$container instanceof ContainerInterface) {
                continue;
            }

            $result = $result->merge($container->getTagged($tag));
        }

        return $result;
    }
}
