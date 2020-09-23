<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Container\Definition\DefinitionInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;

final class ContainerChain implements ContainerWithServiceProvidersInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var PsrContainerInterface[]
     */
    private $chain = [];
    /**
     * @var ContainerInterface
     */
    private $primary;

    /**
     * ContainerChain constructor.
     * @param PsrContainerInterface[] $containers
     */
    public function __construct(iterable $containers)
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
        foreach ($this->chain as $delegate) {
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
        foreach ($containers as $container) {
            $this->addContainer($container);
        }
        return $this;
    }

    /**
     * Add container to the chain
     * @param PsrContainerInterface $container
     * @return $this
     */
    public function addContainer(PsrContainerInterface $container): self
    {
        if ($container instanceof ContainerInterface) {
            $this->setPrimary($container);
        }

        if ($container instanceof ContainerAwareInterface) {
            $container->setContainer($this->getContainer());
        }

        $this->chain[] = $container;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $arguments = [])
    {
        foreach ($this->chain as $container) {
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
        foreach ($this->chain as $container) {
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
        foreach ($this->chain as $container) {
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

        foreach ($this->chain as $container) {
            if (!$container instanceof ContainerInterface) {
                continue;
            }

            $result = $result->merge($container->getTagged($tag));
        }

        return $result;
    }
}
