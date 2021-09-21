<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use spaceonfire\Container\ContainerAwareTrait;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\RawValueHolder;
use Throwable;

final class ArgumentResolver implements ResolverInterface
{
    use ContainerAwareTrait;

    /**
     * ArgumentResolver constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
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

                $type = $parameter->getType();

                if ($parameter->isDefaultValueAvailable()) {
                    $defaultValue = new RawValueHolder($parameter->getDefaultValue());
                } elseif (null !== $type && $type->allowsNull()) {
                    $defaultValue = new RawValueHolder(null);
                } else {
                    $defaultValue = null;
                }

                $argument = new Argument(
                    $name,
                    $type instanceof ReflectionNamedType ? $type->getName() : null,
                    $defaultValue
                );

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
