<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Fixtures\Mapper;

use Cycle\ORM\ORMInterface;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use Warp\Bridge\Cycle\Fixtures\Todo\TodoItemId;
use Warp\Bridge\Cycle\Fixtures\User;
use Warp\Bridge\Cycle\Mapper\HydratorMapper;
use Warp\Bridge\Cycle\Mapper\MapperPluginInterface;
use Warp\Bridge\LaminasHydrator\Strategy\BlameStrategy;
use Warp\Bridge\LaminasHydrator\Strategy\BooleanStrategy;
use Warp\Bridge\LaminasHydrator\Strategy\DateValueStrategy;
use Warp\Bridge\LaminasHydrator\Strategy\ValueObjectStrategy;

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
