<?php

declare(strict_types=1);

namespace spaceonfire\Container\Factory;

use Psr\Container\ContainerInterface;
use spaceonfire\Container\ContainerAwareInterface;
use spaceonfire\Container\DefinitionAggregateInterface;
use spaceonfire\Container\Exception\CannotInstantiateAbstractClassException;
use spaceonfire\Container\Exception\CannotResolveArgumentException;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\FactoryOptionsInterface;
use spaceonfire\Container\InstanceOfAliasContainer;
use spaceonfire\Container\RawValueHolder;
use spaceonfire\Type\AbstractAggregatedType;
use spaceonfire\Type\InstanceOfType;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

/**
 * @template A
 */
final class Argument implements ContainerAwareInterface
{
    private string $name;

    private string $location;

    private TypeInterface $type;

    /**
     * @var RawValueHolder<A>|RawValueHolder<A[]>|null
     */
    private ?RawValueHolder $defaultValue;

    private bool $variadic;

    private ?InstanceOfAliasContainer $container = null;

    /**
     * @param string $name
     * @param string $location
     * @param TypeInterface|null $type
     * @param RawValueHolder<A>|RawValueHolder<A[]>|null $defaultValue
     * @param bool $variadic
     */
    public function __construct(
        string $name,
        string $location,
        ?TypeInterface $type = null,
        ?RawValueHolder $defaultValue = null,
        bool $variadic = false
    ) {
        $type ??= MixedType::new();

        if ($variadic) {
            $defaultValue ??= new RawValueHolder([]);
        }

        if ($type->check(null)) {
            $defaultValue ??= new RawValueHolder(null);
        }

        $this->name = $name;
        $this->location = $location;
        $this->type = $type;
        /** @phpstan-var RawValueHolder<A>|RawValueHolder<A[]>|null $defaultValue */
        $this->defaultValue = $defaultValue;
        $this->variadic = $variadic;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param FactoryOptionsInterface|null $options
     * @return \Generator<A>
     */
    public function resolve(?FactoryOptionsInterface $options = null): \Generator
    {
        $valueContainer = $this->resolveUsingOptions($options)
            ?? $this->resolveUsingType($this->type)
            ?? $this->defaultValue;

        if (null === $valueContainer) {
            throw new CannotResolveArgumentException(
                $this,
                null,
                'Argument has not default value and cannot be resolved through container nor given options.'
            );
        }

        $value = $valueContainer->getValue();

        if ($this->variadic && \is_iterable($value)) {
            return yield from $value;
        }

        /** @phpstan-var A $value */
        return yield $value;
    }

    public function setContainer(?ContainerInterface $container): void
    {
        $this->container = null === $container ? null : InstanceOfAliasContainer::wrap($container);
    }

    public function getContainer(): InstanceOfAliasContainer
    {
        if (null === $this->container) {
            throw new ContainerException('No container implementation has been set.');
        }

        return $this->container;
    }

    /**
     * @param FactoryOptionsInterface|null $options
     * @return RawValueHolder<A>|RawValueHolder<A[]>|null
     */
    private function resolveUsingOptions(?FactoryOptionsInterface $options = null): ?RawValueHolder
    {
        if (null === $options) {
            return null;
        }

        if ($options->hasArgument($this->name)) {
            return new RawValueHolder($options->getArgument($this->name));
        }

        if ($this->variadic && null !== $tag = $options->getArgumentTag($this->name)) {
            return $this->resolveFromContainerByTag($tag);
        }

        if (null !== $alias = $options->getArgumentAlias($this->name)) {
            return $this->resolveFromContainer($alias);
        }

        return null;
    }

    /**
     * @param TypeInterface $type
     * @return RawValueHolder<A>|RawValueHolder<A[]>|null
     */
    private function resolveUsingType(TypeInterface $type): ?RawValueHolder
    {
        if ($type instanceof AbstractAggregatedType) {
            foreach ($type as $subtype) {
                if (null === $output = $this->resolveUsingType($subtype)) {
                    continue;
                }

                return $output;
            }
        }

        if ($type instanceof InstanceOfType) {
            $key = (string)$type;

            if ($this->variadic) {
                return $this->resolveFromContainerByTag($key) ?? $this->resolveFromContainer($key);
            }

            return $this->resolveFromContainer($key);
        }

        return null;
    }

    /**
     * @param string|class-string<A> $alias
     * @return RawValueHolder<A>|null
     */
    private function resolveFromContainer(string $alias): ?RawValueHolder
    {
        try {
            $container = $this->getContainer();

            if (!$container->has($alias)) {
                return null;
            }

            return new RawValueHolder($container->get($alias));
        } catch (CannotInstantiateAbstractClassException $e) {
            return null;
        } catch (\Throwable $e) {
            throw new CannotResolveArgumentException($this, $e);
        }
    }

    /**
     * @param string $tag
     * @return RawValueHolder<A[]>|null
     */
    private function resolveFromContainerByTag(string $tag): ?RawValueHolder
    {
        try {
            $container = $this->getContainer();

            if (!$container->has(DefinitionAggregateInterface::class)) {
                return null;
            }

            /** @var DefinitionAggregateInterface $definitionContainer */
            $definitionContainer = $container->get(DefinitionAggregateInterface::class);

            if (!$definitionContainer->hasTagged($tag)) {
                return null;
            }

            /** @var array<A> $items */
            $items = \iterator_to_array($definitionContainer->getTagged($tag), false);

            return new RawValueHolder($items);
        } catch (CannotInstantiateAbstractClassException $e) {
            return null;
        } catch (\Throwable $e) {
            throw new CannotResolveArgumentException($this, $e);
        }
    }
}
