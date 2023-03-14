<?php

declare(strict_types=1);

namespace Warp\Container\Factory;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use Warp\Container\ContainerAwareTrait;
use Warp\Container\DefinitionInterface;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Factory\Reflection\ReflectionFactoryAggregate;
use Warp\Container\Factory\Reflection\ReflectionInvoker;
use Warp\Container\FactoryAggregateInterface;
use Warp\Container\FactoryOptionsInterface;
use Warp\Container\InstanceOfAliasContainer;
use Warp\Container\InvokerInterface;
use Warp\Container\InvokerOptionsInterface;

/**
 * @template T
 * @implements DefinitionInterface<T>
 */
final class Definition implements DefinitionInterface
{
    use ContainerAwareTrait;

    /**
     * @var string|class-string<T>
     */
    private string $abstract;

    /**
     * @var string|class-string<T>|callable():T|null
     */
    private $concrete;

    private bool $shared;

    private FactoryOptions $options;

    /**
     * @var array<string,true>
     */
    private array $tags = [];

    /**
     * @var Option<T>
     */
    private Option $resolved;

    /**
     * Definition constructor.
     * @param string|class-string<T> $abstract
     * @param T|Option<T>|string|class-string<T>|callable():T|null $concrete
     * @param bool $shared
     */
    public function __construct(string $abstract, $concrete = null, bool $shared = false)
    {
        $this->resolved = None::create();

        $concrete ??= $abstract;

        if ($concrete instanceof Option) {
            /** @phpstan-var Option<T> $concrete */
            $this->resolved = $concrete;
            $concrete = null;
            $shared = true;
        }

        if (\is_object($concrete) && !\is_callable($concrete)) {
            // @phpstan-ignore-next-line
            $this->resolved = new Some($concrete);
            $concrete = null;
            $shared = true;
        }

        $this->abstract = $abstract;
        /** @phpstan-var string|class-string<T>|callable():T|null $concrete */
        $this->concrete = $concrete;
        $this->shared = $shared;
        $this->options = FactoryOptions::new();
    }

    public function getId(): string
    {
        return $this->abstract;
    }

    public function hasTag(string $tag): bool
    {
        return isset($this->tags[$tag]);
    }

    public function getTags(): array
    {
        return \array_keys($this->tags);
    }

    /**
     * @param string $tag
     * @return $this
     */
    public function addTag(string $tag): self
    {
        $this->tags[$tag] = true;

        return $this;
    }

    public function getStaticConstructor(): ?string
    {
        return $this->options->getStaticConstructor();
    }

    /**
     * @param string $constructor
     * @return $this
     */
    public function setStaticConstructor(string $constructor): self
    {
        $this->options->setStaticConstructor($constructor);

        return $this;
    }

    public function getArgumentAlias(string $argument): ?string
    {
        return $this->options->getArgumentAlias($argument);
    }

    /**
     * @param string $argument
     * @param string $alias
     * @return $this
     */
    public function setArgumentAlias(string $argument, string $alias): self
    {
        $this->options->setArgumentAlias($argument, $alias);

        return $this;
    }

    public function getArgumentTag(string $argument): ?string
    {
        return $this->options->getArgumentTag($argument);
    }

    /**
     * @param string $argument
     * @param string $tag
     * @return $this
     */
    public function setArgumentTag(string $argument, string $tag): self
    {
        $this->options->setArgumentTag($argument, $tag);

        return $this;
    }

    /**
     * @param string $argument
     * @param mixed $value
     * @return $this
     */
    public function addArgument(string $argument, $value): self
    {
        $this->options->addArgument($argument, $value);

        return $this;
    }

    public function hasArgument(string $argument): bool
    {
        return $this->options->hasArgument($argument);
    }

    public function getArgument(string $argument): Option
    {
        return $this->options->getArgument($argument);
    }

    /**
     * @param string $method
     * @param InvokerOptionsInterface|null $options
     * @return $this
     */
    public function addMethodCall(string $method, ?InvokerOptionsInterface $options = null): self
    {
        $this->options->addMethodCall($method, $options);

        return $this;
    }

    public function getMethodCalls(): iterable
    {
        return $this->options->getMethodCalls();
    }

    public function make(?FactoryOptionsInterface $options = null)
    {
        if ($this->resolved->isDefined()) {
            return $this->resolved->get();
        }

        $resolved = $this->resolve($options);

        if ($this->shared) {
            $this->resolved = new Some($resolved);
            $this->concrete = null;
        }

        return $resolved;
    }

    /**
     * @param FactoryOptionsInterface|null $options
     * @return T
     */
    private function resolve(?FactoryOptionsInterface $options = null)
    {
        \assert(null !== $this->concrete);

        $concrete = $this->concrete;

        if (\is_callable($concrete)) {
            /** @phpstan-var callable():T $concrete */
            return $this->getInvoker()->invoke($concrete, $options ?? $this->options);
        }

        if ($this->abstract === $concrete) {
            /** @phpstan-var class-string<T> $concrete */
            return $this->getFactoryAggregate()->make($concrete, $options ?? $this->options);
        }

        $container = $this->getContainer();

        if ($container->has($concrete)) {
            /** @phpstan-var string|class-string<T> $concrete */
            return $container->get($concrete);
        }

        throw new ContainerException('Unable to resolve definition.');
    }

    private function getInvoker(): InvokerInterface
    {
        $container = InstanceOfAliasContainer::wrap($this->getContainer());

        if ($container->has(InvokerInterface::class)) {
            return $container->get(InvokerInterface::class);
        }

        return new ReflectionInvoker($container->getContainer());
    }

    private function getFactoryAggregate(): FactoryAggregateInterface
    {
        $container = InstanceOfAliasContainer::wrap($this->getContainer());

        if ($container->has(FactoryAggregateInterface::class)) {
            return $container->get(FactoryAggregateInterface::class);
        }

        return new ReflectionFactoryAggregate($container->getContainer());
    }
}
