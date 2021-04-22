<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\Promise\Reference;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\Expression\ExpressionFactory;

class PrimaryBuilderTest extends AbstractTestCase
{
    /**
     * @dataProvider successDataProvider
     */
    public function testDefault(OrmCapsule $capsule, $input, $expected): void
    {
        $builder = new PrimaryBuilder($capsule->orm(), 'post');

        $ref = $builder->withScope($input)->getReference();

        self::assertSame('post', $ref->__role());
        self::assertSame($expected, $ref->__scope());
    }

    public function successDataProvider(): \Generator
    {
        $capsule = $this->makeOrmCapsule();
        $id = '9a87ba70-89d3-41aa-a5b8-36d18a3a13c6';

        yield [$capsule, $id, ['id' => $id]];
        yield [$capsule, [$id], ['id' => $id]];
        yield [$capsule, ['id' => $id], ['id' => $id]];
        yield [$capsule, new Reference('post', ['id' => $id]), ['id' => $id]];
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testWithWrongScope(OrmCapsule $capsule): void
    {
        $builder = new PrimaryBuilder($capsule->orm(), 'post');

        $this->expectException(\LogicException::class);
        $builder->withScope(['name' => 'Hello, World!']);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testWithWrongReference(OrmCapsule $capsule): void
    {
        $builder = new PrimaryBuilder($capsule->orm(), 'post');

        $this->expectException(\LogicException::class);
        $builder->withScope(new Reference('user', ['id' => '9a87ba70-89d3-41aa-a5b8-36d18a3a13c6']));
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testGetCriteria(OrmCapsule $capsule): void
    {
        $builder = new PrimaryBuilder($capsule->orm(), 'post');

        $builder = $builder->withScope('9a87ba70-89d3-41aa-a5b8-36d18a3a13c6');

        $ef = ExpressionFactory::new();
        $criteria = $builder->getCriteria(Criteria::new()->where($ef->property('active', $ef->same(true))));

        self::assertTrue($criteria->getWhere()->equivalentTo($ef->andX([
            $ef->property('id', $ef->same('9a87ba70-89d3-41aa-a5b8-36d18a3a13c6')),
            $ef->property('active', $ef->same(true)),
        ])));
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testGetCriteriaWithoutScope(OrmCapsule $capsule): void
    {
        $builder = new PrimaryBuilder($capsule->orm(), 'post');

        $this->expectException(\LogicException::class);
        $builder->getCriteria();
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testGetReferenceWithoutScope(OrmCapsule $capsule): void
    {
        $builder = new PrimaryBuilder($capsule->orm(), 'post');

        $this->expectException(\LogicException::class);
        $builder->getReference();
    }
}
