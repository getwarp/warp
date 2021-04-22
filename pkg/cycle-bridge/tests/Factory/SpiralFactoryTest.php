<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Factory;

use Cycle\ORM\ORMInterface;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\Mapper\UserMapper;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Container\CompositeContainer;
use spaceonfire\Container\DefinitionContainer;
use spaceonfire\Container\FactoryContainer;
use spaceonfire\Container\RawValueHolder;

class SpiralFactoryTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testFactory(OrmCapsule $capsule): void
    {
        $container = new CompositeContainer(
            new DefinitionContainer(),
            new FactoryContainer(),
        );

        $container->define(ORMInterface::class, new RawValueHolder($capsule->orm()), true);

        $factory = new SpiralFactory($container);

        $mapper = $factory->make(UserMapper::class, [
            'role' => 'user',
        ]);

        self::assertInstanceOf(UserMapper::class, $mapper);
    }
}
