<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\CollectionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

final class CollectionTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    private const ITERABLE = 'iterable';

    private const VALUE = 'value';

    private const KEY = 'key';

    private TypeFactoryInterface $iterableTypeFactory;

    public function __construct(?TypeFactoryInterface $iterableTypeFactory = null)
    {
        $this->iterableTypeFactory = $iterableTypeFactory ??
            new TypeFactoryAggregate(
                new InstanceOfTypeFactory(),
                new PartialSupportTypeFactory(
                    new BuiltinTypeFactory(),
                    static fn (string $t): bool => \in_array($t, [BuiltinType::ARRAY, BuiltinType::ITERABLE], true)
                ),
            );
    }

    public function supports(string $type): bool
    {
        if (null === $this->parent) {
            return false;
        }

        $this->iterableTypeFactory->setParent($this->parent);

        $typeParts = $this->parseType($type);

        if (null === $typeParts) {
            return false;
        }

        if (!isset($typeParts[self::VALUE])) {
            return false;
        }

        if (!$this->parent->supports($typeParts[self::VALUE])) {
            return false;
        }

        if (
            isset($typeParts[self::ITERABLE]) &&
            !$this->iterableTypeFactory->supports($typeParts[self::ITERABLE])
        ) {
            return false;
        }

        if (isset($typeParts[self::KEY]) && !$this->parent->supports($typeParts[self::KEY])) {
            return false;
        }

        return true;
    }

    public function make(string $type): TypeInterface
    {
        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, CollectionType::class);
        }

        $parsed = $this->parseType($type);

        \assert(null !== $parsed);
        \assert(null !== $parsed[self::VALUE]);
        \assert(null !== $this->parent);

        $valueType = $this->parent->make($parsed[self::VALUE]);
        $keyType = $parsed[self::KEY] ? $this->parent->make($parsed[self::KEY]) : null;
        $iterableType = $parsed[self::ITERABLE] ? $this->iterableTypeFactory->make($parsed[self::ITERABLE]) : null;

        return CollectionType::new($valueType, $keyType, $iterableType);
    }

    /**
     * @param string $type
     * @return array<string,string|null>|null
     */
    private function parseType(string $type): ?array
    {
        $type = $this->removeWhitespaces($type);

        $result = [
            self::ITERABLE => null,
            self::KEY => null,
            self::VALUE => null,
        ];

        if (\str_ends_with($type, '[]')) {
            $result[self::VALUE] = \substr($type, 0, -2) ?: null;
            return $result;
        }

        if ((0 < $openPos = \strpos($type, '<')) && \str_ends_with($type, '>')) {
            $result[self::ITERABLE] = \substr($type, 0, $openPos);
            [$key, $value] = \explode(',', \substr($type, $openPos + 1, -1)) + [null, null];

            if (!$value && !$key) {
                return null;
            }

            if (null === $value) {
                $value = $key;
                $key = null;
            }

            $result[self::KEY] = $key ?: null;
            $result[self::VALUE] = $value ?: null;

            return $result;
        }

        return null;
    }
}
