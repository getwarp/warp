<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\AbstractAggregatedType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

abstract class AbstractAggregatedTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @var class-string<AbstractAggregatedType>
     */
    protected const TYPE_CLASS = AbstractAggregatedType::class;

    final public function __construct()
    {
        if (!\is_subclass_of(static::TYPE_CLASS, AbstractAggregatedType::class)) {
            throw new \RuntimeException(\sprintf(
                '%s::TYPE_CLASS should be class-string of %s.',
                static::class,
                AbstractAggregatedType::class,
            ));
        }

        if (1 !== \strlen(self::constant(static::TYPE_CLASS, 'DELIMITER'))) {
            throw new \RuntimeException(\sprintf('%s::DELIMITER should be 1 symbol string.', static::TYPE_CLASS));
        }
    }

    final public function supports(string $type): bool
    {
        return null !== $this->parse($type);
    }

    final public function make(string $type): TypeInterface
    {
        $parsed = $this->parse($type);

        if (null === $parsed || null === $this->parent) {
            throw new TypeNotSupportedException($type, static::TYPE_CLASS);
        }

        return \call_user_func([static::TYPE_CLASS, 'new'], ...\array_map([$this->parent, 'make'], $parsed));
    }

    /**
     * @param string $type
     * @return array{string,string}|null
     */
    private function parse(string $type): ?array
    {
        if (null === $this->parent) {
            return null;
        }

        $type = $this->removeWhitespaces($type);

        [$left, $right] = $this->split($type);

        if ('' === $left || '' === $right) {
            return null;
        }

        while (!$this->parent->supports($left)) {
            [$appendLeft, $right] = $this->split($right);

            if ('' !== $appendLeft) {
                $left .= self::constant(static::TYPE_CLASS, 'DELIMITER') . $appendLeft;
            }

            if ('' === $right) {
                break;
            }
        }

        if (!$this->parent->supports($right) || !$this->parent->supports($left)) {
            return null;
        }

        return [$left, $right];
    }

    /**
     * @param string $string
     * @return string[]
     */
    private function split(string $string): array
    {
        return \explode(self::constant(static::TYPE_CLASS, 'DELIMITER'), $string, 2) + ['', ''];
    }

    /**
     * @param class-string $className
     * @param string $constName
     * @return mixed
     */
    private static function constant(string $className, string $constName)
    {
        return \constant(\sprintf('%s::%s', $className, $constName));
    }
}
