<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\DisjunctionType;

class CasterFactoryInterfaceTest extends TestCase
{
    public function testDefault(): void
    {
        $factory = CasterFactoryAggregate::default();

        $caster = $factory->make(DisjunctionType::new(BuiltinType::string(), BuiltinType::null()));

        self::assertNotNull($caster);

        self::assertTrue($caster->accepts('string'));
        self::assertTrue($caster->accepts(null));
        self::assertFalse($caster->accepts((object)[]));

        self::assertSame('string', $caster->cast('string'));
        self::assertSame('1.1', $caster->cast(1.1));
        self::assertSame(null, $caster->cast(false));
    }
}
