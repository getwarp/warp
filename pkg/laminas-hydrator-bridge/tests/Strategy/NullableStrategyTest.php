<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use PHPUnit\Framework\TestCase;

class NullableStrategyTest extends TestCase
{
    private function mockStrategy(): StrategyInterface
    {
        return new class implements StrategyInterface {
            public int $extractCalls = 0;
            public int $hydrateCalls = 0;

            public function extract($value, ?object $object = null)
            {
                ++$this->extractCalls;
                return 'bar';
            }

            public function hydrate($value, ?array $data)
            {
                ++$this->hydrateCalls;
                return 'foo';
            }
        };
    }

    public function testSimple(): void
    {
        $strategy = new NullableStrategy($this->mockStrategy());

        self::assertSame('bar', $strategy->extract('foo'));
        self::assertNull($strategy->extract(null));
        self::assertSame('foo', $strategy->hydrate('foo'));
        self::assertNull($strategy->hydrate(null));
    }

    public function testCustomNullValuePredicate(): void
    {
        $nullValues = [0, '', null];
        $strategy = new NullableStrategy($this->mockStrategy(), static fn ($value): bool => in_array($value, $nullValues, true));

        self::assertSame('bar', $strategy->extract('foo'));

        foreach ($nullValues as $value) {
            self::assertNull($strategy->extract($value));
        }

        self::assertSame('foo', $strategy->hydrate('foo'));

        foreach ($nullValues as $value) {
            self::assertNull($strategy->hydrate($value));
        }
    }
}
