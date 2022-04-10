<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Factory;

use Cycle\ORM\ORMInterface;
use PhpOption\Some;
use Warp\Bridge\Cycle\AbstractTestCase;
use Warp\Bridge\Cycle\Fixtures\Mapper\UserMapper;
use Warp\Bridge\Cycle\Fixtures\OrmCapsule;
use Warp\Container\CompositeContainer;
use Warp\Container\DefinitionContainer;
use Warp\Container\FactoryContainer;

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

        $container->define(ORMInterface::class, new Some($capsule->orm()), true);

        $factory = new SpiralFactory($container);

        $mapper = $factory->make(UserMapper::class, [
            'role' => 'user',
        ]);

        self::assertInstanceOf(UserMapper::class, $mapper);
    }
}
