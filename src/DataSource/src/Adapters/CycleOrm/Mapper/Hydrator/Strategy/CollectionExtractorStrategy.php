<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper\Hydrator\Strategy;

use Cycle\ORM\Relation\HasMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\Collection\CollectionInterface;
use Webmozart\Assert\Assert;

class CollectionExtractorStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     *
     * Convert spaceonfire collection to doctrine collection for proper relations handling by Cycle ORM
     *
     * @param CollectionInterface $value
     * @return DoctrineCollection
     * @see HasMany::extract()
     * @see https://github.com/cycle/orm/issues/24
     */
    public function extract($value, ?object $object = null)
    {
        Assert::nullOrIsInstanceOf($value, CollectionInterface::class);
        return new ArrayCollection($value ? $value->all() : []);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        return $value;
    }
}
