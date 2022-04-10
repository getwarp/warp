<?php

declare(strict_types=1);

namespace Warp\Container\Factory\Reflection;

use Psr\Container\ContainerInterface;
use Warp\Common\Factory\StaticConstructorInterface;
use Warp\Container\Exception\CannotInstantiateAbstractClassException;
use Warp\Container\Exception\ContainerException;
use Warp\Container\FactoryInterface;
use Warp\Container\FactoryOptionsInterface;
use Warp\Container\InstanceOfAliasContainer;
use Warp\Container\InvokerInterface;

/**
 * @template T of object
 * @implements FactoryInterface<T>
 */
final class ReflectionFactory implements FactoryInterface
{
    /**
     * @var \ReflectionClass<T>
     */
    private \ReflectionClass $reflection;

    private ReflectionDependencyResolver $dependencyResolver;

    private InvokerInterface $invoker;

    /**
     * @param class-string<T> $class
     */
    public function __construct(string $class, ContainerInterface $container)
    {
        if (!\class_exists($class)) {
            throw new ContainerException(\sprintf('Cannot create factory for not existing class: %s', $class));
        }

        $this->reflection = new \ReflectionClass($class);
        $this->dependencyResolver = new ReflectionDependencyResolver($container);

        $c = InstanceOfAliasContainer::wrap($container);
        $this->invoker = $c->has(InvokerInterface::class)
            ? $c->get(InvokerInterface::class)
            : new ReflectionInvoker($container);
    }

    public function make(?FactoryOptionsInterface $options = null)
    {
        if ($this->reflection->isAbstract()) {
            throw new CannotInstantiateAbstractClassException($this->reflection->getName());
        }

        try {
            $instance = $this->makeWithStaticConstructor($options) ?? $this->makeWithConstructor($options);

            // TODO: fix cyclic references in invoked methods. maybe yield instance first, or use contexted container in invoker.
            return $this->invokeMethods($instance, $options);
        } catch (\ReflectionException $e) {
            throw ContainerException::wrap($e);
        }
    }

    /**
     * @param FactoryOptionsInterface|null $options
     * @return T|null
     * @throws \ReflectionException
     */
    private function makeWithStaticConstructor(?FactoryOptionsInterface $options)
    {
        $className = $this->reflection->getName();
        $staticConstructorName = null === $options ? null : $options->getStaticConstructor();

        if ($this->reflection->implementsInterface(StaticConstructorInterface::class)) {
            $staticConstructorName ??= 'new';
        }

        if (null === $staticConstructorName) {
            return null;
        }

        if (!$this->reflection->hasMethod($staticConstructorName)) {
            throw new ContainerException(\sprintf(
                'Class %s does not have static factory method %s().',
                $className,
                $staticConstructorName,
            ));
        }

        $staticConstructor = $this->reflection->getMethod($staticConstructorName);

        if (!$staticConstructor->isStatic()) {
            throw new ContainerException(\sprintf(
                'Factory method %s::%s() should be static.',
                $className,
                $staticConstructorName,
            ));
        }

        $object = $staticConstructor->invokeArgs(
            null,
            [...$this->dependencyResolver->resolveDependencies($staticConstructor, $options)],
        );

        if (!$object instanceof $className) {
            throw new ContainerException(\sprintf(
                'Invalid factory method implementation. %s::%s() should return instance of %s, but %s given.',
                $className,
                $staticConstructorName,
                $className,
                \get_debug_type($object),
            ));
        }

        return $object;
    }

    /**
     * @param FactoryOptionsInterface|null $options
     * @return T
     * @throws \ReflectionException
     */
    private function makeWithConstructor(?FactoryOptionsInterface $options)
    {
        $className = $this->reflection->getName();

        if (null === $constructor = $this->reflection->getConstructor()) {
            return new $className();
        }

        return $this->reflection->newInstanceArgs(
            \iterator_to_array($this->dependencyResolver->resolveDependencies($constructor, $options), false),
        );
    }

    /**
     * @param T $instance
     * @param FactoryOptionsInterface|null $options
     * @return T
     */
    private function invokeMethods($instance, ?FactoryOptionsInterface $options)
    {
        if (null === $options) {
            return $instance;
        }

        $className = $this->reflection->getName();

        foreach ($options->getMethodCalls() as [$methodName, $methodOptions]) {
            $result = $this->invoker->invoke([$instance, $methodName], $methodOptions);

            if ($result instanceof $className) {
                $instance = $result;
            }
        }

        return $instance;
    }
}
