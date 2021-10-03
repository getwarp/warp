<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use PHPUnit\Framework\TestCase;

class NullCasterTest extends TestCase
{
    /**
     * @dataProvider acceptAllProvider
     */
    public function testAcceptAll($input): void
    {
        $caster = new NullCaster();

        self::assertTrue($caster->accepts($input));
        self::assertNull($caster->cast($input));
    }

    public function acceptAllProvider(): \Generator
    {
        yield [1];
        yield ['1'];
        yield [null];
        yield [false];
        yield [[]];
        yield [(object)[]];
    }

    /**
     * @dataProvider acceptEmptyProvider
     */
    public function testAcceptEmpty($input, bool $accepts = true): void
    {
        $caster = new NullCaster(NullCaster::ACCEPT_EMPTY);

        self::assertSame($accepts, $caster->accepts($input));

        try {
            $casted = $caster->cast($input);

            if ($accepts) {
                self::assertNull($casted);
            }
        } catch (\Throwable $e) {
        } finally {
            if ($accepts) {
                self::assertNotTrue(isset($e));
                return;
            }

            self::assertTrue(isset($e));
            \assert(isset($e));
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function acceptEmptyProvider(): \Generator
    {
        yield [0];
        yield [''];
        yield [null];
        yield [[]];
        yield [false];
        yield [1, false];
        yield ['1', false];
    }

    /**
     * @dataProvider acceptNullProvider
     */
    public function testAcceptNull($input): void
    {
        $accepts = null === $input;
        $caster = new NullCaster(NullCaster::ACCEPT_NULL);

        self::assertSame($accepts, $caster->accepts($input));

        try {
            $casted = $caster->cast($input);

            if ($accepts) {
                self::assertNull($casted);
            }
        } catch (\Throwable $e) {
        } finally {
            if ($accepts) {
                self::assertNotTrue(isset($e));
                return;
            }

            self::assertTrue(isset($e));
            \assert(isset($e));
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function acceptNullProvider(): \Generator
    {
        yield [null];
        yield [0, false];
        yield ['', false];
        yield [[], false];
        yield [false, false];
        yield [1, false];
        yield ['1', false];
    }
}
