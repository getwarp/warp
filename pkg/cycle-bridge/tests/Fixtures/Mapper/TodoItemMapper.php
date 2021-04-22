<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Mapper;

use Cycle\ORM\ORMInterface;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemId;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Mapper\HydratorMapper;
use spaceonfire\Bridge\Cycle\Mapper\MapperPluginInterface;
use spaceonfire\Bridge\LaminasHydrator\Strategy\BlameStrategy;
use spaceonfire\Bridge\LaminasHydrator\Strategy\BooleanStrategy;
use spaceonfire\Bridge\LaminasHydrator\Strategy\DateValueStrategy;
use spaceonfire\Bridge\LaminasHydrator\Strategy\ValueObjectStrategy;

final class TodoItemMapper extends HydratorMapper
{
    use NextPrimaryKeyUuidTrait;

    public function __construct(ORMInterface $orm, string $role, ?MapperPluginInterface $plugin = null)
    {
        parent::__construct($orm, $role, null, $plugin);

        $this->hydrator->setNamingStrategy(MapNamingStrategy::createFromHydrationMap([
            'createdAt' => 'blame.createdAt',
            'createdBy' => 'blame.createdBy',
            'updatedAt' => 'blame.updatedAt',
            'updatedBy' => 'blame.updatedBy',
        ]));

        $this->hydrator->addStrategy('id', new ValueObjectStrategy(TodoItemId::class));
        $this->hydrator->addStrategy('done', new BooleanStrategy([1, '1', 'y', 'Y'], [0, '0', 'n', 'N']));
        $this->hydrator->addStrategy('blame', new BlameStrategy(User::class, [], new DateValueStrategy('Y-m-d H:i:s')));
    }
}
