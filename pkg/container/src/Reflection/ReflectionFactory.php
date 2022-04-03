<?php

declare(strict_types=1);

namespace Warp\Container\Reflection;

use ReflectionClass;
use ReflectionException;
use Warp\Container\Argument\Argument;
use Warp\Container\Argument\ResolverInterface;
use Warp\Container\Exception\CannotInstantiateAbstractClassException;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Exception\NotFoundException;

final class ReflectionFactory
{
    /**
     * @var ResolverInterface
     */
    private $argumentResolver;

    /**
     * ReflectionFactory constructor.
     * @param ResolverInterface $argumentResolver
     */
    public function __construct(ResolverInterface $argumentResolver)
    {
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * Create an instance of given class.
     * @param string $className
     * @param array<string,Argument|mixed> $arguments
     * @return mixed|object
     */
    public function __invoke(string $className, array $arguments = [])
    {
        if (!class_exists($className)) {
            throw new NotFoundException(
                sprintf('Alias (%s) is not an existing class and therefore cannot be resolved', $className)
            );
        }

        try {
            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract()) {
                throw new CannotInstantiateAbstractClassException($className);
            }

            if (null === $constructor = $reflection->getConstructor()) {
                return new $className();
            }

            return $reflection->newInstanceArgs($this->argumentResolver->resolveArguments($constructor, $arguments));
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }
    }
}
