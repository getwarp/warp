<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use PHPUnit\Framework\TestCase;

class JsonStrategyTest extends TestCase
{
    public function testDefault(): void
    {
        $strategy = new JsonStrategy();

        $array = [
            'foo' => 'bar',
        ];
        $json = '{"foo":"bar"}';

        self::assertSame($array, $strategy->hydrate($json));
        self::assertJsonStringEqualsJsonString($json, $strategy->extract($array));
    }

    public function testHydrateThrowExceptionOnInvalidInput(): void
    {
        $strategy = new JsonStrategy();
        $this->expectException(\JsonException::class);
        $strategy->hydrate('{');
    }

    public function testExtractThrowExceptionOnInvalidInput(): void
    {
        $strategy = new JsonStrategy();
        $object = new \stdClass();
        $object->ref = $object;
        $this->expectException(\JsonException::class);
        $strategy->extract($object);
    }

    public function testNotAssociative(): void
    {
        $strategy = new JsonStrategy(false);

        $json = '{"foo":"bar"}';
        $object = $strategy->hydrate($json);

        self::assertIsObject($object);
        self::assertSame('bar', $object->foo);
    }

    public function testFlags(): void
    {
        $strategy = new JsonStrategy(true, \JSON_BIGINT_AS_STRING, \JSON_PRESERVE_ZERO_FRACTION);

        self::assertSame('9223372036854775808', $strategy->hydrate('9223372036854775808'));
        self::assertSame('42.0', $strategy->extract(42.0));
    }
}
