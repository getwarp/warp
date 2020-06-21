<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use ReflectionFunctionAbstract;
use ReflectionMethod;
use spaceonfire\Container\ContainerAwareTrait;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;
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
