<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper\Hydrator\Strategy;

use Cycle\ORM\Promise\Collection\CollectionPromiseInterface;
use Cycle\ORM\Promise\ReferenceInterface;
use Cycle\ORM\Relation\HasMany;
use Cycle\ORM\Relation\ManyToMany;
use Cycle\ORM\Relation\Pivoted\PivotedCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\DataSource\Adapters\CycleOrm\Collection\PivotAwareInterface;
use Webmozart\Assert\Assert;

class CollectionExtractorStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     *
     * Convert spaceonfire collection to doctrine collection for proper relations handling by Cycle ORM
     *
     * @param CollectionInterface $value
     * @return DoctrineCollection|mixed
     * @see HasMany::extract()
     * @see ManyToMany::extract()
     * @see https://github.com/cycle/orm/issues/24
     */
    public function extract($value, ?object $object = null)
    {
        if ($value instanceof ReferenceInterface) {
            return $value;
        }

        if ($value instanceof CollectionPromiseInterface) {
            return $value->getPromise();
        }

        Assert::nullOrIsInstanceOf($value, CollectionInterface::class);
        $elements = $value ? $value->all() : [];

        if ($value instanceof PivotAwareInterface) {
            return new PivotedCollection($elements, $value->getPivotContext());
        }

        return new ArrayCollection($elements);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        return $value;
    }
}
