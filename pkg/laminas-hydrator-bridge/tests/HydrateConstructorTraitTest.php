<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator;

use PHPUnit\Framework\TestCase;
use Warp\Bridge\LaminasHydrator\Fixtures\FixtureConfig;

class HydrateConstructorTraitTest extends TestCase
{
    public function testDefault(): void
    {
        $object = new FixtureConfig([
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ]);

        self::assertSame('foo', $object->foo);
        self::assertSame('bar', $object->bar);
        self::assertSame('baz', $object->baz);
        self::assertSame([
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ], $object->getArrayCopy());
    }
}
