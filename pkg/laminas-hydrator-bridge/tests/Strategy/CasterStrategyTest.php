<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\Cast\ScalarCaster;

class CasterStrategyTest extends TestCase
{
    public function testDefault(): void
    {
        $s = new CasterStrategy(new ScalarCaster(BuiltinType::int()));

        self::assertSame(42, $s->hydrate('42'));
        self::assertSame(42, $s->extract(42));

        $s = new CasterStrategy(new ScalarCaster(BuiltinType::int()), new ScalarCaster(BuiltinType::string()));

        self::assertSame(24, $s->hydrate('24'));
        self::assertSame('24', $s->extract(24));
    }
}
