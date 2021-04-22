<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;

class StdClassMapperTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testDefault(OrmCapsule $capsule): void
    {
        $mapper = new StdClassMapper($capsule->orm(), 'tag');

        $data = [
            'id' => 1,
        ];

        [$entity, $data] = $mapper->init($data);

        $entity = $mapper->hydrate($entity, $data);

        self::assertSame(1, $entity->id);
        self::assertSame($data, $mapper->extract($entity));
    }
}
