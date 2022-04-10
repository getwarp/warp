<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator;

use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use PHPUnit\Framework\TestCase;
use Warp\Bridge\LaminasHydrator\Fixtures\SimpleEntity;

class SafeReflectionHydratorTest extends TestCase
{
    public function testExtract(): void
    {
        $hydrator = new SafeReflectionHydrator();

        self::assertSame([], $hydrator->extract((object)[]));
    }

    public function testHydrate(): void
    {
        $hydrator = new SafeReflectionHydrator();

        $object = (object)[];

        self::assertSame($object, $hydrator->hydrate(['foo' => 'bar'], $object));
    }

    public function testExtractInitializedFieldsFromAnonymousClass(): void
    {
        $hydrator = new SafeReflectionHydrator();

        $instance = new class {
            private string $foo = 'bar';
            private string $bar = 'baz';
            private $baz;
        };

        self::assertSame([
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => null,
        ], $hydrator->extract($instance));
    }

    public function testHydrateExtractAnonymousObject(): void
    {
        $hydrator = new SafeReflectionHydrator();

        $instance = new class {
            private ?string $foo;
        };

        self::assertSame([], $hydrator->extract($instance));

        $hydrated = $hydrator->hydrate([
            'foo' => 'bar',
        ], $instance);

        self::assertSame($instance, $hydrated);
        self::assertSame([
            'foo' => 'bar',
        ], $hydrator->extract($instance));
    }

    public function testHydrateWithStrategiesAndFilter(): void
    {
        $hydrator = new SafeReflectionHydrator();

        $hydrator->setNamingStrategy(MapNamingStrategy::createFromAsymmetricMap([], [
            'alias' => 'value',
        ]));
        $hydrator->addStrategy('value', new ClosureStrategy(null, static fn () => 42));

        $reflection = new \ReflectionClass(SimpleEntity::class);
        $entity = $reflection->newInstanceWithoutConstructor();

        self::assertSame([], $hydrator->extract($entity));

        $entity = $hydrator->hydrate(['alias' => 10], $entity);

        self::assertSame([
            'value' => 42,
        ], $hydrator->extract($entity));

        $hydrator->addFilter('value', static fn () => false);

        self::assertSame([], $hydrator->extract($entity));
    }
}
