<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class ConjunctionType implements Type
{
    /**
     * @var Type[]
     */
    private $conjuncts;

    /**
     * ConjunctionType constructor.
     * @param Type[] $conjuncts
     */
    public function __construct(array $conjuncts)
    {
        Assert::allIsInstanceOf($conjuncts, Type::class);
        $this->conjuncts = $conjuncts;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        foreach ($this->conjuncts as $type) {
            if (!$type->check($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode('&', array_map(static function (Type $type): string {
            return (string)$type;
        }, $this->conjuncts));
    }

    /**
     * @inheritDoc
     */
    public static function supports(string $type): bool
    {
        return count(explode('&', $type)) > 1;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type): Type
    {
        if (!self::supports($type)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not supported by %s', $type, __CLASS__));
        }

        $conjuncts = array_map(static function (string $subType): Type {
            return TypeFactory::create(trim($subType));
        }, explode('&', $type));

        return new self($conjuncts);
    }
}
