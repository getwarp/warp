<?php

declare(strict_types=1);

namespace spaceonfire\Container\Factory\Reflection;

use PhpOption\Some;
use Psr\Container\ContainerInterface;
use spaceonfire\Container\Factory\Argument;
use spaceonfire\Container\FactoryOptionsInterface;
use spaceonfire\Type\AbstractAggregatedType;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\InstanceOfType;
use spaceonfire\Type\IntersectionType;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;
use spaceonfire\Type\UnionType;

/**
 * @internal
 */
final class ReflectionDependencyResolver
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \ReflectionFunctionAbstract $function
     * @param FactoryOptionsInterface|null $options
     * @return \Generator<mixed>
     */
    public function resolveDependencies(
        \ReflectionFunctionAbstract $function,
        ?FactoryOptionsInterface $options
    ): \Generator {
        foreach ($function->getParameters() as $parameter) {
            $arg = $this->makeArgument($parameter);
            $arg->setContainer($this->container);
            yield from $arg->resolve($options);
        }
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return Argument<mixed>
     */
    private function makeArgument(\ReflectionParameter $parameter): Argument
    {
        $function = $parameter->getDeclaringFunction();
        $location = \sprintf('%s(...)', $function->name);
        if ($function instanceof \ReflectionMethod) {
            $location = \sprintf('%s::%s', $function->class, $location);
        }

        return new Argument(
            $parameter->getName(),
            $location,
            self::convertReflectionType($parameter->getType()),
            $parameter->isDefaultValueAvailable() ? new Some($parameter->getDefaultValue()) : null,
            $parameter->isVariadic(),
        );
    }

    private static function convertReflectionType(?\ReflectionType $reflectionType): ?TypeInterface
    {
        $addNullable = null !== $reflectionType && $reflectionType->allowsNull()
            ? static fn (TypeInterface $t) => self::aggregateTypes(UnionType::class, $t, BuiltinType::null())
            : static fn (TypeInterface $t) => $t;

        if ($reflectionType instanceof \ReflectionUnionType) {
            /** @var TypeInterface[] $subtypes */
            $subtypes = \array_map([self::class, 'convertReflectionType'], $reflectionType->getTypes());
            return $addNullable(self::aggregateTypes(UnionType::class, ...$subtypes));
        }

        if ($reflectionType instanceof \ReflectionIntersectionType) {
            /** @var TypeInterface[] $subtypes */
            $subtypes = \array_map([self::class, 'convertReflectionType'], $reflectionType->getTypes());
            return $addNullable(self::aggregateTypes(IntersectionType::class, ...$subtypes));
        }

        if ($reflectionType instanceof \ReflectionNamedType) {
            $name = $reflectionType->getName();

            if ($reflectionType->isBuiltin()) {
                if (MixedType::NAME === $name) {
                    return $addNullable(MixedType::new());
                }

                return $addNullable(BuiltinType::new($name));
            }

            /** @phpstan-var class-string $name */
            return $addNullable(InstanceOfType::new($name));
        }

        return null;
    }

    /**
     * @param class-string<AbstractAggregatedType> $class
     * @param TypeInterface ...$types
     * @return TypeInterface
     */
    private static function aggregateTypes(string $class, TypeInterface ...$types): TypeInterface
    {
        $types = \array_unique($types);

        if (1 < \count($types)) {
            return \call_user_func([$class, 'new'], ...$types);
        }

        return $types[0];
    }
}
