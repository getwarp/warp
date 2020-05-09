<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Tag;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use spaceonfire\DataSource\Adapters\CycleOrm\Repository\AbstractCycleRepository;
use spaceonfire\DataSource\Fixtures\Domain\Tag\TagRepository;

class CycleTagRepository extends AbstractCycleRepository implements TagRepository
{
    /**
     * @inheritDoc
     */
    public static function getTableName(): string
    {
        return 'tags';
    }

    /**
     * @inheritDoc
     */
    public static function getEntityClass(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    protected static function defineInternal(): Entity
    {
        $e = new Entity();
        $e->setRole('tag');

        $e->getFields()->set('id', (new Field())->setType('primary')->setColumn('id')->setPrimary(true));

        return $e;
    }
}
