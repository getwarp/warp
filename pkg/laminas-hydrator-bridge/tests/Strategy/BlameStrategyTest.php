<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use PHPUnit\Framework\TestCase;
use spaceonfire\Clock\DateTimeImmutableValue;
use spaceonfire\DataSource\Blame\Blame;

class BlameStrategyTest extends TestCase
{
    public function testDefault(): void
    {
        $strategy = new BlameStrategy();

        $data = [
            'createdAt' => DateTimeImmutableValue::from('2021-09-18 20:00:00'),
            'updatedAt' => DateTimeImmutableValue::from('2021-09-19 20:00:00'),
            'createdBy' => null,
            'updatedBy' => null,
        ];

        $blame = $strategy->hydrate($data);

        self::assertInstanceOf(Blame::class, $blame);
        self::assertSame($blame, $strategy->hydrate($blame));
        self::assertSame($data, $strategy->extract($blame));
    }

    public function testHydrateInvalid(): void
    {
        $strategy = new BlameStrategy();

        $this->expectException(\InvalidArgumentException::class);

        $strategy->hydrate(42);
    }

    public function testExtractInvalid(): void
    {
        $strategy = new BlameStrategy();

        $this->expectException(\InvalidArgumentException::class);

        $strategy->extract(42);
    }

    public function testWithDateStrategy(): void
    {
        $strategy = new BlameStrategy(null, [], new DateValueStrategy('Y-m-d H:i:s'));

        $data = [
            'createdAt' => '2021-09-18 20:00:00',
            'updatedAt' => '2021-09-19 20:00:00',
            'createdBy' => null,
            'updatedBy' => null,
        ];

        $blame = $strategy->hydrate($data);

        self::assertInstanceOf(Blame::class, $blame);
        self::assertSame($blame, $strategy->hydrate($blame));
        self::assertSame($data, $strategy->extract($blame));
    }
}
