<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Tag;

use Cycle\ORM\ORMInterface;
use spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use spaceonfire\DataSource\Fixtures\Domain\Tag\TagRepositoryInterface;

class CycleTagRepository extends AbstractCycleRepositoryAdapter implements TagRepositoryInterface
{
    public function __construct(ORMInterface $orm)
    {
        parent::__construct('tag', $orm);
    }
}
