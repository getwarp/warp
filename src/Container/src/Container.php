<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use spaceonfire\Container\Argument\Argument;
use spaceonfire\Container\Argument\ArgumentValue;
use spaceonfire\Container\Argument\ResolverInterface;
use spaceonfire\Container\Definition\DefinitionAggregate;
use spaceonfire\Container\Definition\DefinitionAggregateInterface;
use spaceonfire\Container\Definition\DefinitionInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\ServiceProvider\ServiceProviderAggregate;
use spaceonfire\Container\ServiceProvider\ServiceProviderAggregateInterface;
use spaceonfire\Container\ServiceProvider\ServiceProviderInterface;
use Throwable;

final class Container implements
    ContainerWithServiceProvidersInterface,
    ContainerAwareInterface,
    ResolverInterface
{
    use ContainerAwareTrait;

    /**
     * @var DefinitionAggregateInterface
     */
    private $definitions;
    /**
     * @var ServiceProviderAggregateInterface
     */
    private $providers;

    /**
     * Container constructor.
     * @param DefinitionAggregateInterface $definitions
     * @param ServiceProviderAggregateInterface|null $providers
     */
    public function __construct(
        ?DefinitionAggregateInterface $definitions = null,
        ?ServiceProviderAggregateInterface $providers = null
    ) {
        $this->definitions = $definitions ?? new DefinitionAggregate();
        $this->providers = $providers ?? new ServiceProviderAggregate();
        $this->setContainer($this);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;
        $this->providers->setContainer($container);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->definitions->hasDefinition($id) || $this->providers->provides($id) || class_exists($id);
    }

    /**
     * @inheritDoc
     */
    public function add(string $id, $concrete = null, bool $shared = false): DefinitionInterface
    {
        $def = $this->definitions->makeDefinition($id, $concrete, $shared);
        $this->definitions->addDefinition($def);
        return $def;
    }

    /**
     * @inheritDoc
     */
    public function share(string $id, $concrete = null): DefinitionInterface
    {
        return $this->add($id, $concrete, true);
    }

    /**
     * @inheritDoc
     */
    public function addServiceProvider($provider): ContainerWithServiceProvidersInterface
    {
        if ($provider instanceof ServiceProviderInterface) {
            $this->providers->addProvider($provider);
            return $this;
        }

        if (is_string($provider)) {
            return $this->addServiceProvider($this->make($provider));
        }

        throw new InvalidArgumentException(sprintf('Unsupported provider type: %s', gettype($provider)));
    }

    /**
     * @inheritDoc
     */
    public function get($id, array $arguments = [])
    {
        if ($this->definitions->hasDefinition($id)) {
            return $this->definitions->resolve($id, $this);
        }

        if ($this->providers->provides($id)) {
            $this->providers->register($id);

            if (!$this->definitions->hasDefinition($id)) {
                throw new ContainerException(sprintf('Service provider lied about providing (%s) service', $id));
            }

            return $this->get($id, $arguments);
        }

        return $this->make($id, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function make(string $alias, array $arguments = [])
    {
        if (!$this->has($alias)) {
            throw new NotFoundException(
                sprintf('Alias (%s) is not an existing class and therefore cannot be resolved', $alias)
            );
        }

        try {
            $reflection = new ReflectionClass($alias);

            if (null === $constructor = $reflection->getConstructor()) {
                return new $alias();
            }

            return $reflection->newInstanceArgs($this->resolveArguments($constructor, $arguments));
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function invoke(callable $callable, array $arguments = [])
    {
        try {
            if (is_string($callable) && strpos($callable, '::') !== false) {
                $callable = explode('::', $callable);
            }

            if (is_array($callable)) {
                [$object, $method] = $callable;

                $reflection = new ReflectionMethod($object, $method);

                if ($reflection->isStatic()) {
                    $object = null;
                } elseif (!is_object($object)) {
                    $object = $this->getContainer()->get($callable[0]);
                }

                return $reflection->invokeArgs($object, $this->resolveArguments($reflection, $arguments));
            }

            if (is_object($callable)) {
                $reflection = new ReflectionMethod($callable, '__invoke');

                return $reflection->invokeArgs($callable, $this->resolveArguments($reflection, $arguments));
            }

            $reflection = new ReflectionFunction(Closure::fromCallable($callable));

            return $reflection->invokeArgs($this->resolveArguments($reflection, $arguments));
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function resolveArguments(ReflectionFunctionAbstract $reflection, array $arguments = []): array
    {
        $result = [];

        foreach ($reflection->getParameters() as $parameter) {
            try {
                $name = $parameter->getName();

                if (array_key_exists($name, $arguments)) {
                    $v = $arguments[$name];
                    if ($v instanceof Argument) {
                        $v = $v->resolve($this->getContainer());
                    }
                    $result[$name] = $v;
                    continue;
                }

                $class = $parameter->getClass();
                $defaultValue = $parameter->isDefaultValueAvailable()
                    ? new ArgumentValue($parameter->getDefaultValue())
                    : null;

                $argument = new Argument($name, $class === null ? null : $class->getName(), $defaultValue);

                $result[$name] = $argument->resolve($this->getContainer());
            } catch (Throwable $e) {
                $location = $reflection->getName();

                if ($reflection instanceof ReflectionMethod) {
                    $location = $reflection->getDeclaringClass()->getName() . '::' . $location;
                }

                throw new ContainerException(
                    sprintf('Unable to resolve `%s` in {%s}: %s', $parameter->getName(), $location, $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
        }

        return array_values($result);
    }
}
