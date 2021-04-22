<?php

declare(strict_types=1);

namespace spaceonfire\Common\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Common\Fixtures\SingletonValueObject;

class SingletonStorageTraitTest extends TestCase
{
    public function testSameObjectsForSameValue(): void
    {
        $object1 = SingletonValueObject::new('foo');
        $object2 = SingletonValueObject::new('foo');

        self::assertSame($object1, $object2);
    }

    public function testReleaseMemory(): void
    {
        $object1 = SingletonValueObject::new('foo');
        $object2 = SingletonValueObject::new('foo');

        self::assertSame($object1, $object2);

        $id = \spl_object_id($object1);
        unset($object1, $object2);

        $justAnotherObject = SingletonValueObject::new('bar');

        $object1 = SingletonValueObject::new('foo');

        self::assertNotSame($id, \spl_object_id($object1));

        unset($justAnotherObject, $object1);
    }
}
