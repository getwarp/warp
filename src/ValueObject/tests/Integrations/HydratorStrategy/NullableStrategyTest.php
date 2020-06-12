<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class NullableStrategyTest extends TestCase
{
    public function testExtract(): void
    {
        $strategyMock = $this->prophesize(StrategyInterface::class);
        $strategyMock->extract(Argument::any(), Argument::any())
            ->shouldBeCalledTimes(1)
            ->will(function ($args) {
                return $args[0];
            });

        $strategy = new NullableStrategy($strategyMock->reveal());

        self::assertSame('foo', $strategy->extract('foo'));
        self::assertNull($strategy->extract(null));
    }

    public function testHydrate(): void
    {
        $strategyMock = $this->prophesize(StrategyInterface::class);
        $strategyMock->hydrate(Argument::any(), Argument::any())
            ->shouldBeCalledTimes(1)
            ->will(function ($args) {
                return $args[0];
            });

        $strategy = new NullableStrategy($strategyMock->reveal());

        self::assertSame('foo', $strategy->hydrate('foo', []));
        self::assertNull($strategy->hydrate(null, []));
    }
}
