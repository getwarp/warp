<?php

declare(strict_types=1);

namespace spaceonfire\Container\Factory\Reflection;

use Psr\Container\ContainerInterface;
use spaceonfire\Container\ContainerAwareInterface;
use spaceonfire\Container\ContainerAwareTrait;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\Factory\FactoryOptions;
use spaceonfire\Container\InvokerInterface;

final class ReflectionInvoker implements ContainerAwareInterface, InvokerInterface
{
    use ContainerAwareTrait;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    public function invoke(callable $callable, $options = null)
    {
        try {
            $options = FactoryOptions::wrap($options);

            [$reflection, $object] = $this->reflectCallable($callable);

            $dependencyResolver = new ReflectionDependencyResolver($this->getContainer());
            $arguments = [...$dependencyResolver->resolveDependencies($reflection, $options)];

            if ($reflection instanceof \ReflectionMethod) {
                return $reflection->invokeArgs($object, $arguments);
            }

            return $reflection->invokeArgs($arguments);
        } catch (\ReflectionException $e) {
            throw ContainerException::wrap($e);
        }
    }

    /**
     * @param callable $callable
     * @return array{\ReflectionFunction|\ReflectionMethod,object|null}
     * @throws \ReflectionException
     */
    private function reflectCallable(callable $callable): array
    {
        if (\is_string($callable) && \str_contains($callable, '::')) {
            $callable = \explode('::', $callable);
        }

        if (\is_array($callable)) {
            [$object, $method] = $callable;

            $reflection = new \ReflectionMethod($object, $method);

            return [$reflection, $reflection->isStatic() ? null : $object];
        }

        if (\is_object($callable)) {
            $reflection = new \ReflectionMethod($callable, '__invoke');

            return [$reflection, $callable];
        }

        return [new \ReflectionFunction(\Closure::fromCallable($callable)), null];
    }
}
