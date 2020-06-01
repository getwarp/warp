<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class DisjunctionType implements Type
{
    /**
     * @var Type[]
     */
    private $disjuncts;

    /**
     * DisjunctionType constructor.
     * @param Type[] $disjuncts
     */
    public function __construct(array $disjuncts)
    {
        Assert::allIsInstanceOf($disjuncts, Type::class);
        $this->disjuncts = $disjuncts;
    }

    /**
     * @inheritDoc
     */
    public function check($value): bool
    {
        foreach ($this->disjuncts as $type) {
            if ($type->check($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode('|', array_map(static function (Type $type): string {
            return (string)$type;
        }, $this->disjuncts));
    }

    /**
     * @inheritDoc
     */
    public static function supports(string $type): bool
    {
        return count(explode('|', $type)) > 1;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type): Type
    {
        if (!self::supports($type)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not supported by %s', $type, __CLASS__));
        }

        $disjuncts = array_map(static function (string $subType): Type {
            return TypeFactory::create(trim($subType));
        }, explode('|', $type));

        return new self($disjuncts);
    }
}
