<?php

declare(strict_types=1);

namespace spaceonfire\Container\Reflection;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use spaceonfire\Container\Argument\Argument;
use spaceonfire\Container\Argument\ResolverInterface;
use spaceonfire\Container\ContainerAwareInterface;
use spaceonfire\Container\ContainerAwareTrait;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;

final class ReflectionInvoker implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var ResolverInterface
     */
    private $argumentResolver;

    /**
     * ReflectionInvoker constructor.
     * @param ResolverInterface $argumentResolver
     * @param ContainerInterface $container
     */
    public function __construct(ResolverInterface $argumentResolver, ContainerInterface $container)
    {
        $this->argumentResolver = $argumentResolver;
        $this->setContainer($container);
    }

    /**
     * Invoke callable.
     * @param callable $callable
     * @param array<string,Argument|mixed> $arguments
     * @return mixed
     */
    public function __invoke(callable $callable, array $arguments = [])
    {
        try {
            if (is_string($callable) && false !== strpos($callable, '::')) {
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

                return $reflection->invokeArgs(
                    $object,
                    $this->argumentResolver->resolveArguments($reflection, $arguments)
                );
            }

            if (is_object($callable)) {
                $reflection = new ReflectionMethod($callable, '__invoke');

                return $reflection->invokeArgs(
                    $callable,
                    $this->argumentResolver->resolveArguments($reflection, $arguments)
                );
            }

            $reflection = new ReflectionFunction(Closure::fromCallable($callable));

            return $reflection->invokeArgs($this->argumentResolver->resolveArguments($reflection, $arguments));
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }
    }
}
